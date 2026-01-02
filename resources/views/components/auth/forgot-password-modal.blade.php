<flux:modal name="forgot-password-modal" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Forgot your password?') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Enter your email to receive a password reset link') }}</flux:text>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                placeholder="email@example.com"
                data-test="forgot-password-email"
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
                {{ __('Email password reset link') }}
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>
                {{ __('Or, return to') }}
            </span>

            <flux:link href="#" x-on:click.prevent="$flux.modal('forgot-password-modal').close(); $flux.modal('login-form').show()" data-test="forgot-password-switch-to-login">
                {{ __('Log in') }}
            </flux:link>
        </div>
    </div>
</flux:modal>

