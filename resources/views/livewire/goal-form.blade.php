<?php

use App\Models\Goal;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class() extends Component
{
    public string $name = '';

    public ?string $description = null;

    #[Computed]
    public function goals(): Collection
    {
        return $this->user->goals;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->user->goals()->create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        unset($this->goals);

        $this->reset(['name', 'description']);

        Flux::toast(__('Goal added!'), variant: 'success');
    }

    public function updateProgress(Goal $goal, int $progress): void
    {
        $goal->update([
            'progress' => $progress,
            'start_at' => $goal->start_at ?? now(),
            'completed_at' => $progress === 100 ? now() : null,
        ]);

        unset($this->goals);

        if ($progress === 100) {
            Flux::toast(__('Goal completed!'), variant: 'success');
        }
    }

    public function complete(Goal $goal): void
    {
        $goal->update([
            'completed_at' => now(),
            'progress' => 100,
        ]);

        unset($this->goals);

        Flux::toast(__('Goal completed!'), variant: 'success');
    }

    public function cancel(Goal $goal): void
    {
        $goal->update(['canceled_at' => now()]);

        unset($this->goals);

        Flux::toast(__('Goal canceled.'));
    }

    #[Computed]
    public function user(): User
    {
        return auth()->user();
    }
};

?>

<div>
    <div>
        <flux:heading>
            {{ __('Build your path') }}
        </flux:heading>

        <flux:text>
            {{ __('Track what you\'re working toward. See the connection between progress and wellbeing.') }}
        </flux:text>
    </div>

    <div class="max-w-3xl mt-6 space-y-4">
        @foreach ($this->goals as $goal)
            <flux:card size="sm" class="flex justify-between items-center gap-2" :key="'goal-'.$goal->id">
                <div class="flex items-center gap-6">
                    <div class="">
                        <flux:heading>
                            {{ $goal->name }}
                        </flux:heading>

                        <flux:text size="sm">
                            {{ $goal->description }}
                        </flux:text>
                    </div>

                    <div>
                        <flux:badge size="sm" :color="$goal->status->color()">
                            {{ $goal->status->label() }}
                        </flux:badge>
                    </div>
                </div>

                @if ($goal->completed_at === null && $goal->canceled_at === null)
                    <div class="flex items-center w-64 gap-4">
                        <flux:slider
                            :value="$goal->progress"
                            min="0"
                            max="100"
                            step="10"
                            wire:change="updateProgress({{ $goal->id }}, $event.target.value)"
                        />

                        <span class="w-8 text-right tabular-nums">
                            {{ $goal->progress }}%
                        </span>
                    </div>

                    <div class="flex gap-2">
                        <flux:button wire:click="complete({{ $goal->id }})" size="sm" variant="subtle" icon="check" :tooltip="__('Complete')" />

                        <flux:button wire:click="cancel({{ $goal->id }})" size="sm" variant="subtle" icon="x-mark" :tooltip="__('Cancel')" />
                    </div>
                @endif
            </flux:card>
        @endforeach

        <flux:card size="sm">
            <form wire:submit="save" class="w-full">
                <div class="hidden md:flex gap-2 w-full">
                    <flux:input wire:model="name" :placeholder="__('Name')" size="sm"/>

                    <flux:input wire:model="description" :placeholder="__('Description')" size="sm"/>

                    <flux:button type="submit" size="sm" variant="primary" icon="plus" :tooltip="__('Save your goal')" />
                </div>

                <div class="flex md:hidden flex-col gap-2 w-full">
                    <flux:input wire:model="name" :placeholder="__('Name')"/>

                    <flux:input wire:model="description" :placeholder="__('Description')"/>

                    <flux:button type="submit" variant="primary" class="w-full">
                        {{ __('Save your goal') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>
