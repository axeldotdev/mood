<?php

use App\Enums\MoodType;
use App\Models\Mood;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class() extends Component
{
    public array $moods = [];

    public ?string $comment = null;

    public function updatedMoods(): void
    {
        $this->moods = array_slice($this->moods, 0, 2);
    }

    public function save(): void
    {
        if (auth()->check() && $this->todaysMood) {
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

            $this->js("Flux.modal('login-modal').show()");

            return;
        }

        auth()->user()->moods()->create([
            'types' => $this->moods,
            'comment' => $this->comment,
        ]);

        unset($this->todaysMood);

        $this->reset(['moods', 'comment']);

        Flux::toast(__('Your mood has been saved!'));
    }

    #[Computed]
    public function todaysMood(): ?Mood
    {
        if (auth()->guest()) {
            return null;
        }

        return auth()->user()->moods()
            ->whereDate('created_at', today())
            ->first();
    }
};

?>

<div>
    @if (auth()->check() && $this->todaysMood)
        <flux:heading size="xl" level="1" class="text-center">
            {{ __("You've already logged your mood today!") }}
        </flux:heading>

        <flux:text class="mt-2 mb-6 text-center">
            {{ __('Come back tomorrow to log how you feel.') }}
        </flux:text>

        <div class="flex flex-wrap justify-center gap-2">
            @foreach ($this->todaysMood->types as $type)
                <flux:badge size="lg">{{ $type->label() }}</flux:badge>
            @endforeach
        </div>

        @if ($this->todaysMood->comment)
            <flux:text class="mt-6 text-center italic max-w-2xl mx-auto">
                "{{ $this->todaysMood->comment }}"
            </flux:text>
        @endif
    @else
        <flux:heading size="xl" level="1" class="text-center">
            {{ __('What is your mood today?') }}
        </flux:heading>

        <flux:text class="mt-2 mb-6 text-center">
            {{ __('Select one or two adjective and save it') }}
        </flux:text>

        <form wire:submit="save" class="max-w-3xl mx-auto flex flex-col justify-center gap-6">
            <div class="flex flex-col items-center gap-2">
                <flux:checkbox.group wire:model.live="moods" variant="buttons" class="justify-center">
                    @foreach (\App\Enums\MoodType::cases() as $mood)
                        <flux:checkbox wire:key="mood-{{ $mood->value }}" :value="$mood->value" :label="$mood->label()" :disabled="count($moods) >= 2 && !in_array($mood->value, $moods)" size="sm"/>
                    @endforeach
                </flux:checkbox.group>

                <flux:error name="moods" />
            </div>

            <div class="max-w-2xl w-full mx-auto">
                <flux:textarea wire:model="comment" rows="auto" :placeholder="__('Describe what you felt today and what you want to remember')" />
            </div>

            <div class="flex justify-center">
                <flux:button type="submit" variant="primary">
                    {{ __('Save your mood') }}
                </flux:button>
            </div>
        </form>
    @endif
</div>
