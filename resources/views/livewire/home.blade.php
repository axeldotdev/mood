<div>
    <header class="py-16 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="">
            <flux:heading size="6xl" level="1">
                How are you feeling today?
            </flux:heading>

            <flux:subheading size="xl">
                Take a moment to check in with yourself. Track your mood and discover patterns in your emotional wellbeing.
            </flux:subheading>

            <div class="mt-6">
                <flux:modal.trigger name="mood-form">
                    <flux:button variant="primary">
                        Check in
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>

        <div>
            <div class="h-full flex flex-wrap justify-center items-center gap-2">
                @foreach (\App\Enums\MoodType::cases() as $mood)
                    <flux:badge>
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
