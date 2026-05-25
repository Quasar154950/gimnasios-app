<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Cliente;
use App\Models\MensajeCliente;
use App\Models\SaasPago;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'activo',
        'fecha_vencimiento',
        'tipo_app',
        
        'plan',
        'precio_suscripcion',

        'mercadopago_enabled',
        'mercadopago_public_key',
        'mercadopago_access_token',
        'mercadopago_sandbox',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fecha_vencimiento' => 'datetime',
            'activo' => 'boolean',

            'mercadopago_enabled' => 'boolean',
            'mercadopago_sandbox' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
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

    /**
     * Renovar suscripción
     */
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

    /**
     * 🔑 Resetear contraseña (soporte)
     */
    public function resetearPassword(): string
    {
        $nueva = 'Abc' . rand(1000, 9999);

        $this->password = $nueva; // Laravel lo encripta solo
        $this->save();

        return $nueva;
    }
}
