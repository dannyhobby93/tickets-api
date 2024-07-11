<?php

namespace App\Providers;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register() : void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot() : void
    {
        RateLimiter::for('tickets', function (Request $request) {
            // return Limit::perMinute(6)->by($request->user()?->id ?: $request->ip());

            // return $request->user() ?
            //     Limit::perMinute(10)->by($request->ip()) :
            //     Limit::perMinute(5)->by($request->ip());

            if (RateLimiter::tooManyAttempts(
                key: 'transcript:' . $request->ip(),
                maxAttempts: 10
            )) {
                return 'Too many attempts!';
            }
            echo 'Transcript';
            RateLimiter::increment('transcript:' . $request->ip(), amount: 2);
        });
    }
}
