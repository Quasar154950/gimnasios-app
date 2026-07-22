<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\SaasPagoController;
use App\Http\Controllers\MercadoPagoSaasWebhookController;
use App\Http\Controllers\ActivarCuentaSocioController;
use App\Http\Controllers\AvisoController;
use App\Http\Controllers\NovedadController;
use App\Models\User;

Route::redirect('/', '/estudio/sportgym')->name('home');

Route::get('/app', function () {

    if (auth()->check()) {

        if (auth()->user()->role === 'cliente') {
            return redirect()->route('cliente.dashboard');
        }

        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    return redirect()->route('login.estudio', ['slug' => 'sportgym']);

})->name('app.start');


Route::get('/activar-cuenta', [ActivarCuentaSocioController::class, 'show'])
    ->name('activar-cuenta-socio.show');

Route::post('/activar-cuenta', [ActivarCuentaSocioController::class, 'activar'])
    ->name('activar-cuenta-socio.activar');

Route::middleware(['auth', 'verified', 'activo'])->group(function () {

    Route::middleware('role:abogado')->group(function () {

        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // 📢 AVISOS
        Route::resource('avisos', AvisoController::class)
            ->except(['show']);

        // 📰 NOVEDADES
        Route::resource('novedades', NovedadController::class)
            ->except(['show']);

        // 🔥 SUSCRIPCIÓN
        Route::get('/suscripcion', function () {
            $user = auth()->user();
            
            return view('suscripcion.index', compact('user'));
        })->name('suscripcion.index');
        
        Route::post('/suscripcion/pagar', [SaasPagoController::class, 'pagarMiSuscripcion'])
            ->name('suscripcion.pagar');

        Route::post('/renovar/{user}', function (User $user) {
            $user->renovarSuscripcion();
            return back()->with('ok', 'Suscripción renovada +30 días');
        })->name('renovar.suscripcion');

        Route::post('/toggle-activo/{user}', function (User $user) {
            $user->activo = !$user->activo;
            $user->save();
            return back();
        })->name('toggle.activo');

        Route::get('search', [SearchController::class, 'index'])->name('global.search');

        Route::get('clientes/archivados', [ClienteController::class, 'archivados'])->name('clientes.archivados');

        // 💰 PAGOS / CUOTAS GENERALES
        Route::get('pagos', function () {
        $clientes = \App\Models\Cliente::where('abogado_id', auth()->id())
        ->where('archivado', false)
        ->orderBy('nombre')
        ->get();

        return view('pagos.index', compact('clientes'));
        })->name('pagos.index');

        // 💳 MERCADO PAGO
        Route::get('mercadopago', [MercadoPagoController::class, 'index'])
        ->name('mercadopago.index');

        Route::post('mercadopago', [MercadoPagoController::class, 'update'])
        ->name('mercadopago.update');
        
        Route::get('clientes/{cliente}/pagos', [ClienteController::class, 'pagos'])
            ->name('clientes.pagos');
        
        Route::get('clientes/{cliente}/entrenamientos', [ClienteController::class, 'entrenamientos'])
            ->name('clientes.entrenamientos');

        Route::resource('clientes', ClienteController::class);

        Route::post('clientes/{cliente}/asignar-usuario', [ClienteController::class, 'asignarUsuario'])->name('clientes.asignarUsuario');
        Route::post('clientes/{cliente}/crear-acceso', [ClienteController::class, 'crearAcceso'])->name('clientes.crearAcceso');
        Route::post('clientes/{cliente}/reset-password', [ClienteController::class, 'resetPassword'])->name('clientes.resetPassword');
        Route::delete('clientes/{cliente}/quitar-acceso', [ClienteController::class, 'quitarAcceso'])->name('clientes.quitarAcceso');

        Route::patch('clientes/{cliente}/archivar', [ClienteController::class, 'archivar'])->name('clientes.archivar');
        Route::patch('clientes/{cliente}/desarchivar', [ClienteController::class, 'desarchivar'])->name('clientes.desarchivar');

        Route::post('clientes/{cliente}/expedientes', [ExpedienteController::class, 'store'])->name('expedientes.store');
        Route::get('expedientes/{expediente}', [ExpedienteController::class, 'show'])->name('expedientes.show');
        Route::get('expedientes/{expediente}/imprimir', [ExpedienteController::class, 'imprimir'])->name('expedientes.imprimir');
        Route::get('expedientes/{expediente}/edit', [ExpedienteController::class, 'edit'])->name('expedientes.edit');
        Route::put('expedientes/{expediente}', [ExpedienteController::class, 'update'])->name('expedientes.update');
        Route::delete('expedientes/{expediente}', [ExpedienteController::class, 'destroy'])->name('expedientes.destroy');

        Route::post('notas', [NotaController::class, 'store'])->name('notas.store');
        Route::get('notas/{nota}/edit', [NotaController::class, 'edit'])->name('notas.edit');
        Route::put('notas/{nota}', [NotaController::class, 'update'])->name('notas.update');
        Route::delete('notas/{id}', [NotaController::class, 'destroy'])->name('notas.destroy');
        Route::patch('notas/{nota}/toggle-pin', [NotaController::class, 'togglePin'])->name('notas.togglePin');

        Route::get('seguimientos', [SeguimientoController::class, 'index'])->name('seguimientos.index');
        Route::post('seguimientos', [SeguimientoController::class, 'store'])->name('seguimientos.store');
        Route::get('seguimientos/{seguimiento}/edit', [SeguimientoController::class, 'edit'])->name('seguimientos.edit');
        Route::put('seguimientos/{seguimiento}', [SeguimientoController::class, 'update'])->name('seguimientos.update');
        Route::delete('seguimientos/{seguimiento}', [SeguimientoController::class, 'destroy'])->name('seguimientos.destroy');
        Route::patch('seguimientos/{seguimiento}/estado', [SeguimientoController::class, 'cambiarEstado'])->name('seguimientos.cambiarEstado');

        Route::get('historial', [ActividadController::class, 'index'])->name('actividades.index');
        Route::delete('historial/vaciar', [ActividadController::class, 'vaciar'])->name('actividades.vaciar');
        Route::get('turnos', [TurnoController::class, 'index'])->name('turnos.index');
        Route::post('turnos/{turno}/reservar', [TurnoController::class, 'reservar'])
            ->name('turnos.reservar');
        Route::post('turnos/{turno}/reservar-admin', [TurnoController::class, 'reservarAdmin'])
            ->name('turnos.reservar.admin');
        Route::delete('turnos/reservas/{reserva}/cancelar-admin', [TurnoController::class, 'cancelarReservaAdmin'])
             ->name('turnos.reservas.cancelar.admin');

// 👥 ASISTENCIAS
        Route::get('asistencias', [\App\Http\Controllers\AsistenciaController::class, 'index'])
          ->name('asistencias.index');

        Route::get('asistencias/escanear', [\App\Http\Controllers\AsistenciaController::class, 'escanear'])
          ->name('asistencias.escanear');

        Route::post('asistencias/{cliente}/marcar', [\App\Http\Controllers\AsistenciaController::class, 'marcar'])
          ->name('asistencias.marcar');

        Route::get('clientes/{cliente}/qr', [\App\Http\Controllers\AsistenciaController::class, 'qr'])
          ->name('clientes.qr');

        Route::post('asistencias/{asistencia}/salida', [\App\Http\Controllers\AsistenciaController::class, 'salidaManual'])
          ->name('asistencias.salida');
    });
});

// Panel cliente
Route::middleware(['auth', 'role:cliente', 'activo'])->get('/cliente/dashboard', function () {
    return view('cliente.home');
})->name('cliente.dashboard');

Route::middleware(['auth', 'role:cliente', 'activo'])->get('/cliente/mensajes', function () {
    return view('cliente.mensajes');
})->name('cliente.mensajes');

Route::middleware(['auth', 'role:cliente', 'activo'])->get('/cliente/musculacion', function () {
    return view('cliente.musculacion');
})->name('cliente.musculacion');

Route::middleware(['auth', 'role:cliente', 'activo'])->get('/cliente/perfil', function () {

    $cliente = \App\Models\Cliente::where('user_id', auth()->id())->first();

    $reservasActivas = 0;
    $ingresosMes = 0;
    $ultimoIngreso = null;
    $ultimoLogin = auth()->user()->ultimo_login_at;

    if ($cliente) {
        $reservasActivas = \App\Models\ReservaTurno::where('cliente_id', $cliente->id)
            ->whereHas('turno', function ($query) use ($cliente) {
                $query->where('abogado_id', $cliente->abogado_id)
                ->whereDate('fecha', '>=', now()->toDateString());
            })
            ->count();

        $ingresosMes = \App\Models\Asistencia::where('cliente_id', $cliente->id)
    ->whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->count();

$ultimoIngreso = \App\Models\Asistencia::where('cliente_id', $cliente->id)
    ->orderByDesc('created_at')
    ->first();
    }

    return view('cliente.perfil', compact(
        'cliente',
        'reservasActivas',
        'ingresosMes',
        'ultimoIngreso',
        'ultimoLogin',
    ));

})->name('cliente.perfil');

Route::middleware(['auth', 'role:cliente', 'activo'])->get('/cliente/turnos', [TurnoController::class, 'index'])
    ->name('cliente.turnos');

Route::middleware(['auth', 'role:cliente', 'activo'])->get('/cliente/mis-reservas', function () {

    $cliente = \App\Models\Cliente::where('user_id', auth()->id())->first();

    $misReservas = collect();

    if ($cliente) {
        $misReservas = \App\Models\ReservaTurno::with('turno')
            ->where('cliente_id', $cliente->id)
            ->whereHas('turno', function ($query) use ($cliente) {
                $query->where('abogado_id', $cliente->abogado_id)
                    ->where(function ($q) {
                      $q->whereDate('fecha', '>', now()->toDateString())
                          ->orWhere(function ($q2) {
                              $q2->whereDate('fecha', now()->toDateString())
                              ->whereTime('hora_fin', '>=', now()->format('H:i:s'));
                      });
                });
            })
            ->get()
            ->sortBy(fn($r) => $r->turno->fecha . ' ' . $r->turno->hora_inicio);
    }

    return view('cliente.mis-reservas', compact('cliente', 'misReservas'));

})->name('cliente.mis-reservas');

Route::middleware(['auth', 'role:cliente', 'activo'])->get('/cliente/cuota', function () {

    $cliente = \App\Models\Cliente::where('user_id', auth()->id())->first();

    return view('cliente.cuota', compact('cliente'));

})->name('cliente.cuota');

Route::middleware(['auth', 'role:cliente', 'activo'])->get(
    '/cliente/pagar-cuota',
    [MercadoPagoController::class, 'crearPago']
)->name('cliente.pagar-cuota');

Route::middleware(['auth', 'role:cliente', 'activo'])->get('/cliente/mi-qr', [\App\Http\Controllers\AsistenciaController::class, 'miQr'])
    ->name('cliente.mi-qr');

Route::middleware(['auth', 'role:cliente', 'activo'])->post('/cliente/turnos/{turno}/reservar', [TurnoController::class, 'reservar'])
    ->name('cliente.turnos.reservar');

Route::middleware(['auth', 'role:cliente', 'activo'])->delete('/cliente/reservas/{reserva}/cancelar', [TurnoController::class, 'cancelarReserva'])
    ->name('cliente.reservas.cancelar');

Route::middleware(['auth', 'role:cliente', 'activo'])->get('/cliente/expedientes/{expediente}/imprimir', [ExpedienteController::class, 'imprimir'])
    ->name('cliente.expedientes.imprimir');

// Panel soporte
Route::middleware(['auth'])->get('/soporte', function () {
    $user = auth()->user();

    if (!$user || $user->email !== 'soporte@tuempresa.com') {
        abort(403);
    }

    return view('soporte.index');
});

// 💳 PAGOS SAAS
Route::middleware(['auth'])->group(function () {

    Route::post('/soporte/saas-pagos/{user}/pagar', [SaasPagoController::class, 'pagar'])
        ->name('soporte.saas.pagar');

    Route::get('/soporte/saas-pagos/exito', [SaasPagoController::class, 'exito']);

    Route::get('/soporte/saas-pagos/error', [SaasPagoController::class, 'error']);

    Route::get('/soporte/saas-pagos/pendiente', [SaasPagoController::class, 'pendiente']);

});

// 🔔 WEBHOOK MERCADO PAGO SAAS
Route::post('/webhooks/mercadopago/saas', [MercadoPagoSaasWebhookController::class, 'handle'])
    ->name('webhooks.mercadopago.saas');

// 🔑 RESET PASSWORD (SOLO SOPORTE)
Route::middleware(['auth'])->post('/soporte/reset-password/{user}', function (User $user) {

    $userAuth = auth()->user();

    if (!$userAuth || $userAuth->email !== 'soporte@tuempresa.com') {
        abort(403);
    }

    $nueva = $user->resetearPassword();

    return back()->with('password_generada', $nueva);

})->name('soporte.reset.password');

// ✏️ EDITAR VENCIMIENTO
Route::middleware(['auth'])->get('/soporte/{user}/editar-vencimiento', function (User $user) {

    $userAuth = auth()->user();

    if (!$userAuth || $userAuth->email !== 'soporte@tuempresa.com') {
        abort(403);
    }

    return view('soporte.editar-vencimiento', compact('user'));

})->name('soporte.editar.vencimiento');

// 💾 GUARDAR VENCIMIENTO
Route::middleware(['auth'])->post('/soporte/{user}/guardar-vencimiento', function (User $user, \Illuminate\Http\Request $request) {

    $userAuth = auth()->user();

    if (!$userAuth || $userAuth->email !== 'soporte@tuempresa.com') {
        abort(403);
    }

    $request->validate([
    'fecha_vencimiento' => ['required', 'date'],
    'plan' => ['required', 'string'],
    'precio_suscripcion' => ['required', 'integer', 'min:0'],
    ]);

    $user->fecha_vencimiento = $request->fecha_vencimiento;
    $user->plan = $request->plan;
    $user->precio_suscripcion = $request->precio_suscripcion;

    $user->save();

    return redirect('/soporte')->with('success', 'Fecha de vencimiento actualizada correctamente.');

})->name('soporte.guardar.vencimiento');

// 💾 BACKUP DEL SISTEMA
Route::middleware(['auth'])->post('/soporte/backup', function () {

    $userAuth = auth()->user();

    // SOLO SOPORTE
    if (!$userAuth || $userAuth->email !== 'soporte@tuempresa.com') {
        abort(403);
    }

    // CREAR CARPETA SI NO EXISTE
    if (!file_exists(base_path('backups'))) {
        mkdir(base_path('backups'), 0777, true);
    }

    // NOMBRE DEL ARCHIVO
    $filename = 'backup-railway-' . now()->format('Y-m-d-H-i-s') . '.sql';

    $filepath = base_path('backups/' . $filename);

    // VARIABLES DB RAILWAY
    $host = env('DB_HOST');
    $port = env('DB_PORT', 5432);
    $database = env('DB_DATABASE');
    $username = env('DB_USERNAME');
    $password = env('DB_PASSWORD');

    // PASSWORD TEMPORAL PARA PG_DUMP
    putenv('PGPASSWORD=' . $password);

    // COMANDO BACKUP LINUX
    $command =
        'pg_dump ' .
        '-h "' . $host . '" ' .
        '-p "' . $port . '" ' .
        '-U "' . $username . '" ' .
        '-d "' . $database . '" ' .
        '-f "' . $filepath . '" 2>&1';

    // EJECUTAR
    exec($command, $output, $result);

    // ERROR
    if ($result !== 0) {
        dd($command, $output, $result);
    }

    // DESCARGAR
    return response()->download($filepath)->deleteFileAfterSend(true);

})->name('soporte.backup');

// 👁 VER COMO USUARIO
Route::middleware(['auth'])->post('/soporte/ver-como/{user}', function (User $user) {

    $userAuth = auth()->user();

    // SOLO SOPORTE
    if (!$userAuth || $userAuth->email !== 'soporte@tuempresa.com') {
        abort(403);
    }

    // SOLO ABOGADOS
    if ($user->role !== 'abogado') {
        abort(403);
    }

    // GUARDAMOS SOPORTE ORIGINAL
    session([
        'soporte_original_id' => $userAuth->id,
        'soporte_ver_como_id' => $user->id,
    ]);

    // LOGIN COMO USUARIO
    auth()->login($user);

    // REDIRECCIÓN
    return redirect('/dashboard');

})->name('soporte.ver-como');

// ↩ VOLVER A SOPORTE
Route::middleware(['auth'])->post('/soporte/volver', function () {

    $soporteId = session('soporte_original_id');

    if (!$soporteId) {
        abort(403);
    }

    $soporte = User::find($soporteId);

    if (!$soporte) {
        abort(403);
    }

    // LOGIN SOPORTE ORIGINAL
    auth()->login($soporte);

    // LIMPIAR SESIONES
    session()->forget([
        'soporte_original_id',
        'soporte_ver_como_id',
    ]);

    return redirect('/soporte');

})->name('soporte.volver');


// 🔐 Login por estudio
Route::get('/estudio/{slug}', function ($slug) {

    $userEstudio = User::where('slug_estudio', $slug)->first();

    if (!$userEstudio) {
        abort(404);
    }

    session([
        'slug_estudio' => $slug,
        'login_context' => 'estudio'
    ]);

    return response()
        ->view('auth.login-estudio', [
            'userEstudio' => $userEstudio,
        ])
        ->cookie('last_login_context', 'estudio', 60 * 24 * 30)
        ->cookie('last_estudio_slug', $slug, 60 * 24 * 30);

})->name('login.estudio');

// 🔐 Login soporte
Route::get('/soporte/login', function () {

    session(['login_context' => 'soporte']);

    return response()
        ->view('pages::auth.login')
        ->cookie('last_login_context', 'soporte', 60 * 24 * 30);

})->name('login.soporte');


require __DIR__ . '/settings.php';
