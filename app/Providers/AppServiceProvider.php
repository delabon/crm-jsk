<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
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
            return Limit::perMinute(5)->by(($request->email ?? '') . $request->ip());
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
    }
}
