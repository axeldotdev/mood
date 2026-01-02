<?php

use App\Actions\SavePendingMoodAction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Component;

new class() extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(StatefulGuard $guard, SavePendingMoodAction $savePendingMood): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        event(new Registered($user));

        $guard->login($user);

        session()->regenerate();

        $savePendingMood->execute($user);

        $this->redirect(route('dashboard'), navigate: true);
    }
};

?>

<flux:modal name="register-form" class="mx-4 sm:mx-auto">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Create an account') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Enter your details below to create your account') }}</flux:text>
        </div>

        <form wire:submit="register" class="flex flex-col gap-6">
            <flux:input
                wire:model="name"
                :label="__('Name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
                data-test="register-name"
            />

            <flux:input
                wire:model="email"
                :label="__('Email address')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
                data-test="register-email"
            />

            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
                data-test="register-password"
            />

            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
                data-test="register-password-confirmation"
            />

            <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                <span wire:loading.remove wire:target="register">{{ __('Create account') }}</span>
                <span wire:loading wire:target="register">{{ __('Creating account...') }}</span>
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link href="#" x-on:click.prevent="$flux.modal('register-form').close(); $flux.modal('login-form').show()" data-test="switch-to-login">
                {{ __('Log in') }}
            </flux:link>
        </div>
    </div>
</flux:modal>
