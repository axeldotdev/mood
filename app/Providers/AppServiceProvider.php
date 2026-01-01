<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Override;

final class AppServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void {}

    public function boot(): void
    {
        $this->bootCommands();
        $this->bootDates();
        $this->bootEvents();
        $this->bootExceptions();
        $this->bootGates();
        $this->bootModels();
        $this->bootPassword();
        $this->bootRoutes();
        $this->bootUrl();
    }

    private function bootCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }

    private function bootDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function bootEvents(): void {}

    private function bootExceptions(): void
    {
        RequestException::dontTruncate();
    }

    private function bootGates(): void {}

    private function bootModels(): void
    {
        Model::shouldBeStrict();
        Model::unguard();
    }

    private function bootPassword(): void
    {
        Password::defaults(fn (): Password => Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised());
    }

    private function bootRoutes(): void {}

    private function bootUrl(): void
    {
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
