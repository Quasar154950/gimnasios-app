<?php

namespace App\Providers;

use App\Listeners\ActualizarUltimoLogin;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Throwable;

class AppServiceProvider extends ServiceProvider
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

        // Fuerza a Laravel a usar estilos de Tailwind para los links.
        Paginator::useTailwind();

        // Actualiza la fecha del último ingreso al iniciar sesión.
        Event::listen(
            Login::class,
            ActualizarUltimoLogin::class,
        );

        /*
        |--------------------------------------------------------------------------
        | ENLACE PÚBLICO DEL STORAGE
        |--------------------------------------------------------------------------
        | En Railway crea public/storage automáticamente si no existe.
        */

        $this->crearEnlaceStorage();
    }

    /**
     * Crea el enlace public/storage cuando no existe.
     */
    private function crearEnlaceStorage(): void
    {
        $origen = storage_path('app/public');
        $destino = public_path('storage');

        if (file_exists($destino) || is_link($destino)) {
            return;
        }

        try {
            if (! is_dir($origen)) {
                mkdir($origen, 0775, true);
            }

            symlink($origen, $destino);
        } catch (Throwable $error) {
            Log::warning(
                'No se pudo crear el enlace público de storage.',
                [
                    'error' => $error->getMessage(),
                ],
            );
        }
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
}