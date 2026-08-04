<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Cliente;
use App\Models\MensajeCliente;
use App\Models\SaasPago;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'activo',
        'fecha_vencimiento',
        'tipo_app',

        'ultimo_login_at',

        'plan',
        'precio_suscripcion',

        'mercadopago_enabled',
        'mercadopago_public_key',
        'mercadopago_access_token',
        'mercadopago_refresh_token',
        'mercadopago_user_id',
        'mercadopago_token_expires_at',
        'mercadopago_connected_at',
        'mercadopago_sandbox',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fecha_vencimiento' => 'datetime',
            'ultimo_login_at' => 'datetime',
            'activo' => 'boolean',

            'mercadopago_enabled' => 'boolean',
            'mercadopago_sandbox' => 'boolean',
            'mercadopago_token_expires_at' => 'datetime',
            'mercadopago_connected_at' => 'datetime',
        ];
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class);
    }

    public function mensajesEnviados()
    {
        return $this->hasMany(MensajeCliente::class);
    }

    public function saasPagos()
    {
        return $this->hasMany(SaasPago::class);
    }

    public function dispositivosPush(): HasMany
    {
    return $this->hasMany(DispositivoPush::class);
    }

    public function renovarSuscripcion(int $dias = 30): void
    {
        $fechaBase = $this->fecha_vencimiento && $this->fecha_vencimiento->isFuture()
            ? $this->fecha_vencimiento
            : now();

        $this->update([
            'fecha_vencimiento' => $fechaBase->copy()->addDays($dias),
            'activo' => true,
        ]);
    }

    public function resetearPassword(): string
    {
        $nueva = 'Abc' . rand(1000, 9999);

        $this->password = $nueva;
        $this->save();

        return $nueva;
    }

    public function sendPasswordResetNotification($token): void
{
    ResetPassword::toMailUsing(function ($notifiable, $token) {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $slug = $notifiable->slug_estudio
            ?? $notifiable->cliente?->abogado?->slug_estudio
            ?? 'sportgym';

        $nombreGimnasio = match ($slug) {
            'demo' => 'DemoGym',
            'sportgym' => 'SportGym',
            default => Str::headline($slug),
        };

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Restablecer contraseña - {$nombreGimnasio}")
            ->greeting('Hola')
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace vencerá en 60 minutos.')
            ->line('Si no solicitaste este cambio, no hace falta hacer nada.')
            ->salutation("Saludos, {$nombreGimnasio}");
    });

        $this->notify(new ResetPassword($token));
    }
}
