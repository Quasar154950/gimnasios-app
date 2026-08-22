<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'cliente_id',
        'numero_comprobante',
        'monto',
        'metodo_pago',
        'observacion',
        'fecha_pago',
        'fecha_base',
        'vencimiento_cuota',
        'origen',
        'estado',
        'mercadopago_payment_id',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'fecha_base' => 'date',
        'vencimiento_cuota' => 'date',
        'monto' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
