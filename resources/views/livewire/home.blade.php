<div>
    <header class="py-16 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-6">
        <div class="max-w-2xl lg:content-center">
            <flux:heading size="4xl" level="1">
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

    <flux:card>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 p-6 md:p-12">
            <div class="">
                <flux:heading size="xl" level="2">
                    {{ __('Set your goals. See what moves you forward.') }}
                </flux:heading>

                <flux:subheading size="lg">
                    {{ __('Define your objectives for the year and watch how your mood aligns with your progress. Discover which goals energize you—and which ones drain you.') }}
                </flux:subheading>

                <div class="mt-6">
                    <flux:modal.trigger name="login-form">
                        <flux:button variant="primary">
                            {{ __('Set my path') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            <div class="lg:content-center lg:px-16">
                <flux:checkbox.group variant="cards" class="flex-col">
                    <flux:checkbox checked :label="__('Meet new people')"/>
                    <flux:checkbox checked :label="__('Monitoring my health')"/>
                    <flux:checkbox :label="__('Start my business')"/>
                </flux:checkbox.group>
            </div>
        </div>
    </flux:card>

    <flux:modal name="mood-form" class="max-w-5xl! mx-4 md:mx-auto">
        <livewire:mood-form />
    </flux:modal>
</div>
