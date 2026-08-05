<?php

namespace App\Livewire\NotificacionesPush;

use App\Models\Cliente;
use App\Models\DispositivoPush;
use App\Models\NotificacionPush;
use App\Services\FirebasePushService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Throwable;

class Create extends Component
{
    public string $titulo = '';

    public string $mensaje = '';

    public string $destinatario = 'todos';

    public ?int $clienteId = null;

    public string $modoEnvio = 'ahora';

    public ?string $programadaPara = null;

    protected function rules(): array
    {
        return [
            'titulo' => [
                'required',
                'string',
                'max:255',
            ],

            'mensaje' => [
                'required',
                'string',
                'max:2000',
            ],

            'destinatario' => [
                'required',
                'in:todos,cliente,cuota_vencida',
            ],

            'clienteId' => [
                'nullable',
                'integer',
            ],

            'modoEnvio' => [
                'required',
                'in:ahora,programar',
            ],

            'programadaPara' => [
                'nullable',
                'date',
                'after:now',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'titulo.required' => 'Escribí un título.',
            'titulo.max' => 'El título no puede superar los 255 caracteres.',

            'mensaje.required' => 'Escribí el mensaje de la notificación.',
            'mensaje.max' => 'El mensaje no puede superar los 2000 caracteres.',

            'destinatario.in' => 'Seleccioná un destinatario válido.',

            'clienteId.integer' => 'El socio seleccionado no es válido.',

            'modoEnvio.in' => 'Seleccioná una forma de envío válida.',

            'programadaPara.date' => 'La fecha programada no es válida.',
            'programadaPara.after' => 'La fecha programada debe ser posterior al momento actual.',
        ];
    }

    public function updatedDestinatario(string $valor): void
    {
        if ($valor !== 'cliente') {
            $this->clienteId = null;
        }
    }

    public function updatedModoEnvio(string $valor): void
    {
        if ($valor !== 'programar') {
            $this->programadaPara = null;
        }
    }

    public function guardar(
        FirebasePushService $firebasePushService
    ): void {
        $this->validate();

        if (
            $this->destinatario === 'cliente'
            && ! $this->clienteId
        ) {
            $this->addError(
                'clienteId',
                'Seleccioná el socio que recibirá la notificación.'
            );

            return;
        }

        if (
            $this->modoEnvio === 'programar'
            && ! $this->programadaPara
        ) {
            $this->addError(
                'programadaPara',
                'Seleccioná la fecha y hora de envío.'
            );

            return;
        }

        $clienteSeleccionado = null;

        if ($this->clienteId) {
            $clienteSeleccionado = Cliente::query()
                ->where('abogado_id', auth()->id())
                ->findOrFail($this->clienteId);
        }

        $notificacion = NotificacionPush::create([
            'user_id' => auth()->id(),
            'cliente_id' => $clienteSeleccionado?->id,
            'titulo' => trim($this->titulo),
            'mensaje' => trim($this->mensaje),
            'tipo' => 'manual',
            'destinatario' => $this->destinatario,
            'estado' => 'pendiente',
            'programada_para' => $this->modoEnvio === 'programar'
                ? $this->programadaPara
                : null,
            'cantidad_enviada' => 0,
        ]);

        /*
        |--------------------------------------------------------------------------
        | NOTIFICACIÓN PROGRAMADA
        |--------------------------------------------------------------------------
        */

        if ($this->modoEnvio === 'programar') {
            session()->flash(
                'success',
                'La notificación fue programada correctamente.'
            );

            $this->redirectRoute(
                'notificaciones-push.index',
                navigate: true
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | OBTENER DISPOSITIVOS DESTINATARIOS
        |--------------------------------------------------------------------------
        */

        $dispositivos = DispositivoPush::query()
            ->whereHas(
                'user.cliente',
                function (Builder $query): void {
                    $query->where(
                        'abogado_id',
                        auth()->id()
                    );

                    if ($this->destinatario === 'cliente') {
                        $query->where(
                            'id',
                            $this->clienteId
                        );
                    }

                    if ($this->destinatario === 'cuota_vencida') {
                        $query
                            ->whereNotNull(
                                'fecha_vencimiento_cuota'
                            )
                            ->whereDate(
                                'fecha_vencimiento_cuota',
                                '<',
                                now()->toDateString()
                            );
                    }
                }
            )
            ->get();

        if ($dispositivos->isEmpty()) {
            $notificacion->update([
                'estado' => 'error',
                'error' => 'No se encontraron dispositivos registrados para los destinatarios seleccionados.',
            ]);

            session()->flash(
                'error',
                'La notificación fue guardada, pero no hay celulares registrados para esos destinatarios.'
            );

            $this->redirectRoute(
                'notificaciones-push.index',
                navigate: true
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | ENVIAR PUSH
        |--------------------------------------------------------------------------
        */

        $cantidadEnviada = 0;
        $errores = [];

        foreach ($dispositivos as $dispositivo) {
            try {
                $resultado = $firebasePushService->enviar(
                    token: $dispositivo->token,
                    titulo: trim($this->titulo),
                    mensaje: trim($this->mensaje),
                    data: [
                        'tipo' => 'manual',
                        'pantalla' => 'inicio',
                        'notificacion_id' => (string) $notificacion->id,
                    ],
                );

                if ($resultado['ok'] ?? false) {
                    $cantidadEnviada++;

                    continue;
                }

                $errores[] = $resultado['error']
                    ?? 'Firebase devolvió un error desconocido.';
            } catch (Throwable $e) {
                $errores[] = $e->getMessage();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR HISTORIAL
        |--------------------------------------------------------------------------
        */

        if ($cantidadEnviada > 0) {
            $notificacion->update([
                'estado' => 'enviada',
                'enviada_at' => now(),
                'cantidad_enviada' => $cantidadEnviada,
                'error' => $errores !== []
                    ? implode(' | ', array_unique($errores))
                    : null,
            ]);

            session()->flash(
                'success',
                $cantidadEnviada === 1
                    ? 'La notificación fue enviada correctamente a 1 dispositivo.'
                    : "La notificación fue enviada correctamente a {$cantidadEnviada} dispositivos."
            );
        } else {
            $notificacion->update([
                'estado' => 'error',
                'cantidad_enviada' => 0,
                'error' => $errores !== []
                    ? implode(' | ', array_unique($errores))
                    : 'No se pudo enviar la notificación.',
            ]);

            session()->flash(
                'error',
                'La notificación fue guardada, pero no pudo enviarse.'
            );
        }

        $this->redirectRoute(
            'notificaciones-push.index',
            navigate: true
        );
    }

    public function render()
    {
        $clientes = Cliente::query()
            ->where('abogado_id', auth()->id())
            ->where('archivado', false)
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
            ]);

        return view(
            'livewire.notificaciones-push.create',
            [
                'clientes' => $clientes,
            ]
        );
    }
}
