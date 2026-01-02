<div>
    <header class="py-16 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-6">
        <div class="max-w-2xl">
            <flux:heading size="6xl" level="1">
                {{ __('How are you feeling today?') }}
            </flux:heading>

            <flux:subheading size="xl">
                {{ __('Take a moment to check in with yourself. Track your mood and discover patterns in your emotional wellbeing.') }}
            </flux:subheading>

            <div class="mt-6">
                <flux:modal.trigger name="mood-form">
                    <flux:button variant="primary">
                        {{ __('Check in') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>

        <div class="lg:content-center">
            <div class="max-w-xl flex flex-wrap lg:justify-center lg:items-center gap-2">
                @foreach (\App\Enums\MoodType::cases() as $mood)
                    <flux:badge :color="$mood->badgeColor()">
                        {{ $mood->label() }}
                    </flux:badge>
                @endforeach
            </div>
        </div>
    </header>

    <flux:modal name="mood-form" class="max-w-5xl!">
        <livewire:mood-form />
    </flux:modal>
</div>
