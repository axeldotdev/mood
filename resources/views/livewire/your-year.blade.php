<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class() extends Component
{
    public ?int $selectedMonth = null;

    /**
     * @return array<int, array{month: string, pleasant: int, unpleasant: int}|array{day: string, pleasant: int, unpleasant: int}>
     */
    #[Computed]
    public function chartData(): array
    {
        if ($this->selectedMonth !== null) {
            return $this->getDailyChartData();
        }

        return $this->getMonthlyChartData();
    }

    #[Computed]
    public function xAxisField(): string
    {
        return $this->selectedMonth !== null ? 'day' : 'month';
    }

    #[Computed]
    public function hasPleasantData(): bool
    {
        return collect($this->chartData)->sum('pleasant') > 0;
    }

    #[Computed]
    public function hasUnpleasantData(): bool
    {
        return collect($this->chartData)->sum('unpleasant') > 0;
    }

    /**
     * @return array<int, array{month: string, pleasant: int, unpleasant: int}>
     */
    private function getMonthlyChartData(): array
    {
        $months = [];

        foreach (range(1, 12) as $month) {
            $months[$month] = ['pleasant' => 0, 'unpleasant' => 0];
        }

        $moods = Auth::user()
            ->moods()
            ->whereYear('created_at', now()->year)
            ->get();

        foreach ($moods as $mood) {
            $month = $mood->created_at->month;

            foreach ($mood->types as $type) {
                $category = $type->category();
                $months[$month][$category]++;
            }
        }

        return collect($months)->map(fn (array $counts, int $month): array => [
            'month' => now()->setMonth($month)->format('M'),
            'pleasant' => $counts['pleasant'],
            'unpleasant' => $counts['unpleasant'],
        ])->values()->all();
    }

    /**
     * @return array<int, array{day: string, pleasant: int, unpleasant: int}>
     */
    private function getDailyChartData(): array
    {
        $year = now()->year;
        $daysInMonth = now()->setYear($year)->setMonth($this->selectedMonth)->daysInMonth;

        $days = [];

        foreach (range(1, $daysInMonth) as $day) {
            $days[$day] = ['pleasant' => 0, 'unpleasant' => 0];
        }

        $moods = Auth::user()
            ->moods()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $this->selectedMonth)
            ->get();

        foreach ($moods as $mood) {
            $day = $mood->created_at->day;

            foreach ($mood->types as $type) {
                $category = $type->category();
                $days[$day][$category]++;
            }
        }

        return collect($days)->map(fn (array $counts, int $day): array => [
            'day' => (string) $day,
            'pleasant' => $counts['pleasant'],
            'unpleasant' => $counts['unpleasant'],
        ])->values()->all();
    }
};

?>

<div>
    <flux:card class="space-y-6">
        <div class="flex justify-between">
            <div>
                <flux:heading>
                    {{ __('Your moods this year') }}
                </flux:heading>

                <flux:text>
                    {{ __('They are divided between pleasant and unpleasant moods') }}
                </flux:text>
            </div>

            <div>
                <flux:select wire:model.live="selectedMonth" placeholder="{{ __('All year') }}" clearable variant="listbox" size="sm">
                    @foreach (range(1, 12) as $m)
                        <flux:select.option value="{{ $m }}">
                            {{ now()->setMonth($m)->format('F') }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <flux:chart wire:key="chart-{{ $selectedMonth ?? 'year' }}" :value="$this->chartData">
            <flux:chart.viewport class="aspect-3/1">
                <flux:chart.svg>
                    @if ($this->hasPleasantData)
                        <flux:chart.line field="pleasant" class="text-sky-500 dark:text-sky-400" />
                        <flux:chart.point field="pleasant" class="text-sky-500 dark:text-sky-400" />
                    @endif

                    @if ($this->hasUnpleasantData)
                        <flux:chart.line field="unpleasant" class="text-amber-500 dark:text-amber-400" />
                        <flux:chart.point field="unpleasant" class="text-amber-500 dark:text-amber-400" />
                    @endif

                    <flux:chart.axis axis="x" :field="$this->xAxisField">
                        <flux:chart.axis.tick />
                        <flux:chart.axis.line />
                    </flux:chart.axis>
                    <flux:chart.axis axis="y">
                        <flux:chart.axis.grid />
                        <flux:chart.axis.tick />
                    </flux:chart.axis>
                    <flux:chart.cursor />
                </flux:chart.svg>

                <flux:chart.tooltip>
                    <flux:chart.tooltip.heading :field="$this->xAxisField" />

                    @if ($this->hasPleasantData)
                        <flux:chart.tooltip.value field="pleasant" label="Pleasant" />
                    @endif

                    @if ($this->hasUnpleasantData)
                        <flux:chart.tooltip.value field="unpleasant" label="Unpleasant" />
                    @endif
                </flux:chart.tooltip>
            </flux:chart.viewport>

            <div class="flex justify-center gap-4 pt-4">
                <flux:chart.legend label="Pleasant">
                    <flux:chart.legend.indicator class="bg-sky-500" />
                </flux:chart.legend>

                <flux:chart.legend label="Unpleasant">
                    <flux:chart.legend.indicator class="bg-amber-500" />
                </flux:chart.legend>
            </div>
        </flux:chart>
    </flux:card>
</div>
