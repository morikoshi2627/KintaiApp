<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });


        // Fortify の LoginResponse をカスタマイズ
        Fortify::authenticateUsing(function (LoginRequest $request) {
            $credentials = $request->only('email', 'password');

            // hidden input "guard" で判定
            $guard = $request->input('guard', 'web');

            if (Auth::guard($guard)->attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();

                // 管理者なら管理者トップへ
                if ($guard === 'admin') {
                    return redirect()->intended('/admin/attendances');
                }

                // 一般ユーザー
                return Auth::guard($guard)->user();
            }

            return null;
        });
    }
}