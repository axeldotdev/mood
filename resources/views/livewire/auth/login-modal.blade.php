<?php

use App\Actions\SavePendingMoodAction;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class() extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login(StatefulGuard $guard, SavePendingMoodAction $savePendingMood): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! $guard->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        session()->regenerate();

        $savePendingMood->execute(auth()->user());

        $this->redirect(route('home'), navigate: true);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
};

?>

<flux:modal name="login-modal" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Log in to your account') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Enter your email and password below to log in') }}</flux:text>
        </div>

        <form wire:submit="login" class="flex flex-col gap-6">
            <flux:input
                wire:model="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <div class="relative">
                <flux:input
                    wire:model="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" href="#" x-on:click.prevent="$flux.modal('login-modal').close(); $flux.modal('forgot-password-modal').show()" data-test="switch-to-forgot-password">
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <flux:checkbox wire:model="remember" :label="__('Remember me')" />

            <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                <span wire:loading.remove wire:target="login">{{ __('Log in') }}</span>
                <span wire:loading wire:target="login">{{ __('Logging in...') }}</span>
            </flux:button>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __("Don't have an account?") }}</span>
                <flux:link href="#" x-on:click.prevent="$flux.modal('login-modal').close(); $flux.modal('register-modal').show()" data-test="switch-to-register">
                    {{ __('Sign up') }}
                </flux:link>
            </div>
        @endif
    </div>
</flux:modal>
