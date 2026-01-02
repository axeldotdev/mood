<?php

use App\Enums\MoodType;
use App\Models\Mood;
use Carbon\CarbonImmutable;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class() extends Component
{
    public string $selectedDay;

    public array $moods = [];

    public ?string $comment = null;

    public function mount(): void
    {
        $this->selectedDay ??= today()->toDateString();
    }

    public function updatedMoods(): void
    {
        $this->moods = array_slice($this->moods, 0, 2);
    }

    public function save(): void
    {
        if (auth()->check() && $this->dayMood) {
            return;
        }

        $this->validate(
            rules: [
                'moods' => ['required', 'array', 'min:1', 'max:2'],
                'moods.*' => ['required', Rule::enum(MoodType::class)],
                'comment' => ['nullable', 'string', 'max:5000'],
            ],
            messages: [
                'moods.required' => 'Please select at least one mood.',
                'moods.min' => 'Please select at least one mood.',
                'moods.max' => 'You can only select up to two moods.',
                'moods.*.enum' => 'The selected mood is invalid.',
                'comment.max' => 'Your comment is too long (maximum 5000 characters).',
            ],
            attributes: [
                'moods' => 'mood selection',
                'comment' => 'comment',
            ],
        );

        if (auth()->guest()) {
            session(['pending_mood' => [
                'types' => $this->moods,
                'comment' => $this->comment,
            ]]);

            $this->js("Flux.modal('login-form').show()");

            return;
        }

        auth()->user()->moods()->create([
            'types' => $this->moods,
            'comment' => $this->comment,
            'created_at' => CarbonImmutable::parse($this->selectedDay),
        ]);

        unset($this->dayMood);

        $this->reset(['moods', 'comment']);

        Flux::toast(__('Your mood has been saved!'));
    }

    #[Computed]
    public function dayMood(): ?Mood
    {
        if (auth()->guest()) {
            return null;
        }

        return auth()->user()->moods()
            ->whereDate('created_at', $this->selectedDay)
            ->first();
    }

    #[Computed]
    public function isToday(): bool
    {
        return $this->selectedDay === today()->toDateString();
    }
};

?>

<div>
    @if (auth()->check() && $this->dayMood)
        <flux:heading size="xl" level="1" class="text-center">
            {{ $this->isToday ? __("You've already logged your mood today!") : __("You've already logged your mood for this day!") }}
        </flux:heading>

        <flux:text class="mt-2 mb-6 text-center">
            {{ $this->isToday ? __('Come back tomorrow to log how you feel.') : __('This day already has a mood entry.') }}
        </flux:text>

        <div class="flex flex-wrap justify-center gap-2">
            @foreach ($this->dayMood->types as $type)
                <flux:badge size="lg">{{ $type->label() }}</flux:badge>
            @endforeach
        </div>

        @if ($this->dayMood->comment)
            <flux:text class="mt-6 text-center italic max-w-2xl mx-auto">
                "{{ $this->dayMood->comment }}"
            </flux:text>
        @endif
    @else
        <div>
            <flux:heading size="lg">
                {{ $this->isToday ? __('What is your mood today?') : __('What was your mood yesterday?') }}
            </flux:heading>

            <flux:text class="mt-2">
                {{ __('Select one or two adjective and save it') }}
            </flux:text>
        </div>

        <form wire:submit="save" class="max-w-3xl flex flex-col gap-6 mt-6">
            <div class="gap-2">
                <flux:checkbox.group wire:model.live="moods" variant="buttons">
                    @foreach (\App\Enums\MoodType::cases() as $mood)
                        <flux:checkbox wire:key="mood-{{ $mood->value }}" :value="$mood->value" :label="$mood->label()" :disabled="count($moods) >= 2 && !in_array($mood->value, $moods)" size="sm"/>
                    @endforeach
                </flux:checkbox.group>

                <flux:error name="moods" />
            </div>

            <div class="max-w-2xl w-full">
                <flux:textarea wire:model="comment" rows="auto" :placeholder="$this->isToday ? __('Describe what you felt today and what you want to remember') : __('Describe what you felt yesterday and what you want to remember')" />
            </div>

            <div class="">
                <flux:button type="submit" variant="primary">
                    {{ __('Save your mood') }}
                </flux:button>
            </div>
        </form>
    @endif
</div>
