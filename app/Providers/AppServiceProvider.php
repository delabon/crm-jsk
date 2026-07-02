<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\RolePermissionEventSubscriber;
use App\Models\Country;
use App\Models\Region;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Squire\Repository;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimits();
        $this->eventSubscribers();
        $this->registerSquireModels();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    private function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    private function configureRateLimits(): void
    {
        RateLimiter::for('login', static function (Request $request) {
            return Limit::perMinute(5)->by(($request->email ?? '').$request->ip());
        });

        RateLimiter::for('register', static function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('email-verification-send', static function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('password-email', static function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('password-update', static function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('users-manage', static function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('accounts-manage', static function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('contacts-manage', static function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }

    private function eventSubscribers(): void
    {
        Event::subscribe(RolePermissionEventSubscriber::class);
    }

    private function registerSquireModels(): void
    {
        Repository::registerSource(Region::class, 'en', base_path('vendor/squirephp/regions-en/resources/data.csv'));
        Repository::registerSource(Country::class, 'en', base_path('vendor/squirephp/countries-en/resources/data.csv'));
    }
}
