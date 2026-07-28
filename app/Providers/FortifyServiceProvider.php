<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\PasswordResetResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | RESPUESTA DESPUÉS DEL LOGIN
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    $user = auth()->user();

                    if ($request->wantsJson()) {
                        return new \Illuminate\Http\JsonResponse([
                            'two_factor' => false,
                        ]);
                    }

                    if ($user && $user->email === 'soporte@tuempresa.com') {
                        return redirect('/soporte');
                    }

                    if ($user && $user->role === 'cliente') {
                        return redirect('/cliente/dashboard');
                    }

                    return redirect('/dashboard');
                }
            };
        });

        /*
        |--------------------------------------------------------------------------
        | RESPUESTA DESPUÉS DEL LOGOUT
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(LogoutResponse::class, function () {
            return new class implements LogoutResponse {
                public function toResponse($request)
                {
                    $context = $request->cookie('last_login_context');
                    $slug = $request->cookie('last_estudio_slug');

                    if ($context === 'soporte') {
                        return redirect('/soporte/login');
                    }

                    if ($context === 'estudio' && $slug) {
                        return redirect('/estudio/' . $slug);
                    }

                    return redirect('/login');
                }
            };
        });

        /*
        |--------------------------------------------------------------------------
        | RESPUESTA DESPUÉS DE RESTABLECER LA CONTRASEÑA
        |--------------------------------------------------------------------------
        |
        | El socio no es enviado al login web.
        | Se muestra una pantalla final indicando que debe volver a Flutter.
        |
        */

        $this->app->singleton(PasswordResetResponse::class, function () {
            return new class implements PasswordResetResponse {
                public function toResponse($request)
                {
                    if ($request->wantsJson()) {
                        return new \Illuminate\Http\JsonResponse([
                            'message' => trans('passwords.reset'),
                        ]);
                    }

                    return response()->view('auth.password-reset-success');
                }
            };
        });
    }

    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();

        Fortify::authenticateUsing(function (Request $request) {

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return null;
            }

            $slugEstudio = session('slug_estudio');
            $context = session('login_context');

            /*
            |--------------------------------------------------------------------------
            | ACCESO DE SOPORTE
            |--------------------------------------------------------------------------
            */

            if ($context === 'soporte') {
                if ($user->email !== 'soporte@tuempresa.com') {
                    return null;
                }

                return $user;
            }

            /*
            |--------------------------------------------------------------------------
            | ACCESO POR GIMNASIO
            |--------------------------------------------------------------------------
            */

            if ($context === 'estudio') {

                if (! $slugEstudio) {
                    return null;
                }

                /*
                |--------------------------------------------------------------
                | ADMINISTRADOR DEL GIMNASIO
                |--------------------------------------------------------------
                */

                if ($user->role === 'abogado') {
                    if ($user->slug_estudio !== $slugEstudio) {
                        return null;
                    }

                    return $user;
                }

                /*
                |--------------------------------------------------------------
                | SOCIO DEL GIMNASIO
                |--------------------------------------------------------------
                */

                if ($user->role === 'cliente') {
                    $abogado = User::where('role', 'abogado')
                        ->where('slug_estudio', $slugEstudio)
                        ->first();

                    if (! $abogado) {
                        return null;
                    }

                    $cliente = \App\Models\Cliente::where('user_id', $user->id)
                        ->where('abogado_id', $abogado->id)
                        ->first();

                    if (! $cliente) {
                        return null;
                    }

                    return $user;
                }

                return null;
            }

            return null;
        });
    }

    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    private function configureViews(): void
    {
        Fortify::loginView(fn () => view('pages::auth.login'));

        Fortify::verifyEmailView(
            fn () => view('pages::auth.verify-email')
        );

        Fortify::twoFactorChallengeView(
            fn () => view('pages::auth.two-factor-challenge')
        );

        Fortify::confirmPasswordView(
            fn () => view('pages::auth.confirm-password')
        );

        Fortify::registerView(
            fn () => view('pages::auth.register')
        );

        Fortify::resetPasswordView(
            fn () => view('pages::auth.reset-password')
        );

        Fortify::requestPasswordResetLinkView(
            fn () => view('pages::auth.forgot-password')
        );
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower($request->input(Fortify::username()))
                . '|' .
                $request->ip()
            );

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
