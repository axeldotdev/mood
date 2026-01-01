<?php

use App\Enums\MoodType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class() extends Component
{
    public ?int $selectedMonth = null;

    public string $viewMode = 'categorised';

    #[Computed]
    public function chartData(): array
    {
        if ($this->viewMode === 'detailed') {
            return $this->selectedMonth !== null
                ? $this->getDetailedDailyChartData()
                : $this->getDetailedMonthlyChartData();
        }

        return $this->selectedMonth !== null
            ? $this->getDailyChartData()
            : $this->getMonthlyChartData();
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
     * @return array<int, array<int, array{types: \Illuminate\Database\Eloquent\Casts\ArrayObject<int, \App\Enums\MoodType>, comment: ?string}>>
     */
    #[Computed]
    public function moodsByDate(): array
    {
        $moods = Auth::user()
            ->moods()
            ->whereYear('created_at', now()->year)
            ->get();

        $indexed = [];

        foreach ($moods as $mood) {
            $month = $mood->created_at->month;
            $day = $mood->created_at->day;
            $indexed[$month][$day] = [
                'types' => $mood->types,
                'comment' => $mood->comment,
            ];
        }

        return $indexed;
    }

    /**
     * @return array<string, bool>
     */
    #[Computed]
    public function moodTypesWithData(): array
    {
        $result = [];

        foreach (MoodType::cases() as $type) {
            $result[$type->value] = collect($this->chartData)->sum($type->value) > 0;
        }

        return $result;
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

    /**
     * @return array<int, array<string, int|string>>
     */
    private function getDetailedMonthlyChartData(): array
    {
        $months = [];

        foreach (range(1, 12) as $month) {
            $months[$month] = collect(MoodType::cases())
                ->mapWithKeys(fn (MoodType $type): array => [$type->value => 0])
                ->all();
        }

        $moods = Auth::user()
            ->moods()
            ->whereYear('created_at', now()->year)
            ->get();

        foreach ($moods as $mood) {
            $month = $mood->created_at->month;

            foreach ($mood->types as $type) {
                $months[$month][$type->value]++;
            }
        }

        return collect($months)->map(function (array $counts, int $month): array {
            return array_merge(
                ['month' => now()->setMonth($month)->format('M')],
                $counts
            );
        })->values()->all();
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    private function getDetailedDailyChartData(): array
    {
        $year = now()->year;
        $daysInMonth = now()->setYear($year)->setMonth($this->selectedMonth)->daysInMonth;

        $days = [];

        foreach (range(1, $daysInMonth) as $day) {
            $days[$day] = collect(MoodType::cases())
                ->mapWithKeys(fn (MoodType $type): array => [$type->value => 0])
                ->all();
        }

        $moods = Auth::user()
            ->moods()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $this->selectedMonth)
            ->get();

        foreach ($moods as $mood) {
            $day = $mood->created_at->day;

            foreach ($mood->types as $type) {
                $days[$day][$type->value]++;
            }
        }

        return collect($days)->map(function (array $counts, int $day): array {
            return array_merge(
                ['day' => (string) $day],
                $counts
            );
        })->values()->all();
    }
};

?>

<div class="space-y-6">
    <flux:card class="space-y-6">
        <div class="flex justify-between">
            <div>
                <flux:heading>
                    {{ __('Mood Trends') }}
                </flux:heading>

                <flux:text>
                    {{ __('See how your feelings evolved over time') }}
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:select wire:model.live="viewMode" variant="listbox" size="sm">
                    <flux:select.option value="categorised">
                        {{ __('Categorised') }}
                    </flux:select.option>

                    <flux:select.option value="detailed">
                        {{ __('Detailed') }}
                    </flux:select.option>
                </flux:select>

                <flux:select wire:model.live="selectedMonth" placeholder="{{ __('All year') }}" clearable variant="listbox" size="sm">
                    @foreach (range(1, 12) as $m)
                        <flux:select.option value="{{ $m }}">
                            {{ now()->setMonth($m)->format('F') }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <flux:chart wire:key="chart-{{ $viewMode }}-{{ $selectedMonth ?? 'year' }}" :value="$this->chartData">
            <flux:chart.viewport class="aspect-3/1">
                <flux:chart.svg>
                    @if ($viewMode === 'categorised')
                        @if ($this->hasPleasantData)
                            <flux:chart.line field="pleasant" class="text-sky-500 dark:text-sky-400" />
                            <flux:chart.point field="pleasant" class="text-sky-500 dark:text-sky-400" />
                        @endif

                        @if ($this->hasUnpleasantData)
                            <flux:chart.line field="unpleasant" class="text-amber-500 dark:text-amber-400" />
                            <flux:chart.point field="unpleasant" class="text-amber-500 dark:text-amber-400" />
                        @endif
                    @else
                        @foreach (App\Enums\MoodType::cases() as $type)
                            @if ($this->moodTypesWithData[$type->value])
                                <flux:chart.line :field="$type->value" :class="$type->chartColor()" />
                                <flux:chart.point :field="$type->value" :class="$type->chartColor()" />
                            @endif
                        @endforeach
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

                    @if ($viewMode === 'categorised')
                        @if ($this->hasPleasantData)
                            <flux:chart.tooltip.value field="pleasant" label="{{ __('Pleasant') }}" />
                        @endif

                        @if ($this->hasUnpleasantData)
                            <flux:chart.tooltip.value field="unpleasant" label="{{ __('Unpleasant') }}" />
                        @endif
                    @else
                        @foreach (App\Enums\MoodType::cases() as $type)
                            @if ($this->moodTypesWithData[$type->value])
                                <flux:chart.tooltip.value :field="$type->value" :label="$type->label()" />
                            @endif
                        @endforeach
                    @endif
                </flux:chart.tooltip>
            </flux:chart.viewport>

            <div class="flex flex-wrap justify-center gap-4 pt-4">
                @if ($viewMode === 'categorised')
                    <flux:chart.legend label="{{ __('Pleasant') }}">
                        <flux:chart.legend.indicator class="bg-sky-500" />
                    </flux:chart.legend>

                    <flux:chart.legend label="{{ __('Unpleasant') }}">
                        <flux:chart.legend.indicator class="bg-amber-500" />
                    </flux:chart.legend>
                @else
                    @foreach (App\Enums\MoodType::cases() as $type)
                        @if ($this->moodTypesWithData[$type->value])
                            <flux:chart.legend :label="$type->label()">
                                <flux:chart.legend.indicator :class="$type->chartLegendColor()" />
                            </flux:chart.legend>
                        @endif
                    @endforeach
                @endif
            </div>
        </flux:chart>
    </flux:card>

    <flux:card class="space-y-6">
        <div>
            <flux:heading>
                {{ __('Mood Calendar') }}
            </flux:heading>

            <flux:text>
                {{ __('Every feeling, every day') }}
            </flux:text>
        </div>

        <flux:table container:class="max-h-128">
            <flux:table.columns sticky class="bg-white">
                <flux:table.column sticky>
                    {{ __('Day') }}
                </flux:table.column>

                @foreach (range(1, 12) as $m)
                    <flux:table.column wire:key="'month-'.$m" :class="now()->month === $m ? 'text-sky-500!' : ''">
                        {{ now()->setMonth($m)->format('F') }}
                    </flux:table.column>
                @endforeach
            </flux:table.columns>

            <flux:table.rows>
                @foreach (range(1, 31) as $d)
                    <flux:table.row>
                        <flux:table.cell sticky class="bg-white">
                            {{ $d }}
                        </flux:table.cell>

                        @foreach (range(1, 12) as $m)
                            <flux:table.cell wire:key="'month-'.$m.'-day-'.$d">
                                @if (isset($this->moodsByDate[$m][$d]))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($this->moodsByDate[$m][$d]['types'] as $type)
                                            <flux:tooltip :content="$type->label()">
                                                <flux:badge size="sm" :color="$type->badgeColor()">
                                                    {{ $type->emoji() }}
                                                </flux:badge>
                                            </flux:tooltip>
                                        @endforeach

                                        @if ($this->moodsByDate[$m][$d]['comment'])
                                            <flux:tooltip :content="$this->moodsByDate[$m][$d]['comment']">
                                                <flux:badge size="sm">
                                                    <x-slot name="icon">
                                                        <flux:icon.chat-bubble-bottom-center-text variant="micro"/>
                                                    </x-slot>
                                                </flux:badge>
                                            </flux:tooltip>
                                        @endif
                                    </div>
                                @endif
                            </flux:table.cell>
                        @endforeach
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
