<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-800 font-mono antialiased">
        <flux:header container>
            @auth
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            @endauth

            <a href="{{ route('home') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                <x-app-logo />
            </a>

            @auth
                <flux:navbar class="-mb-px max-lg:hidden">
                    <flux:navbar.item icon="calendar" :href="route('dashboard')" wire:navigate>
                        {{ __('Today') }}
                    </flux:navbar.item>

                    <flux:navbar.item icon="calendar-days" :href="route('your-year')" wire:navigate>
                        {{ __('Your year') }}
                    </flux:navbar.item>
                </flux:navbar>
            @endauth

            <flux:spacer />

            @auth
                <flux:dropdown position="top" align="end">
                    <flux:profile
                        class="cursor-pointer"
                        :initials="auth()->user()->initials()"
                        avatar:color="sky"
                    />

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <flux:avatar :initials="auth()->user()->initials()" color="sky" />

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <flux:text variant="strong" class="truncate font-semibold">
                                            {{ auth()->user()->name }}
                                        </flux:text>

                                        <flux:text size="sm" class="truncate">
                                            {{ auth()->user()->email }}
                                        </flux:text>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            @else
                <div class="flex gap-2">
                    <flux:modal.trigger name="login-form">
                        <flux:button>
                            {{ __('Sign in') }}
                        </flux:button>
                    </flux:modal.trigger>

                    <div class="hidden sm:flex">
                        <flux:modal.trigger name="register-form">
                            <flux:button variant="primary">
                                {{ __('Sign up') }}
                            </flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>

                <livewire:auth.login-modal />
                <livewire:auth.register-modal />
                <x-auth.forgot-password-modal />
            @endauth
        </flux:header>

        @auth
            <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

                <a href="{{ route('home') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                    <x-app-logo />
                </a>

                <flux:navlist variant="outline">
                    <flux:navlist.item icon="calendar" :href="route('dashboard')" wire:navigate>
                        {{ __('Today') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="calendar-days" :href="route('your-year')" wire:navigate>
                        {{ __('Your year') }}
                    </flux:navlist.item>
                </flux:navlist>
            </flux:sidebar>
        @endauth

        <flux:main :container="true">
            {{ $slot }}

            <footer class="mt-6">
                <flux:text size="sm" class="text-center">
                    &copy; Copyright {{ date('Y') }} Axel Charpentier. {{ __('All rights reserved') }}.
                </flux:text>
            </footer>
        </flux:main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast position="bottom center" />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
