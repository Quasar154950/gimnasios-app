<?php

namespace App\Livewire\NotificacionesPush;

use App\Models\Cliente;
use App\Models\NotificacionPush;
use App\Services\NotificacionPushService;
use Livewire\Component;

class Create extends Component
{
    public string $titulo = '';

    public string $mensaje = '';

    public string $destinatario = 'todos';

    public ?int $clienteId = null;

    public string $pantalla = 'inicio';

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

            'pantalla' => [
                'required',
                'in:inicio,reservas,cuota,mensajes,rutinas,novedades,perfil,qr,musculacion',
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

            'pantalla.required' => 'Seleccioná la pantalla que se abrirá.',
            'pantalla.in' => 'La pantalla seleccionada no es válida.',

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
        NotificacionPushService $notificacionPushService
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
            'pantalla' => $this->pantalla,
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
        | ENVIAR AHORA
        |--------------------------------------------------------------------------
        */

        $resultado = $notificacionPushService->enviar(
            $notificacion
        );

        if ($resultado['ok'] ?? false) {
            $cantidad = (int) (
                $resultado['cantidad_enviada'] ?? 0
            );

            session()->flash(
                'success',
                $cantidad === 1
                    ? 'La notificación fue enviada correctamente a 1 dispositivo.'
                    : "La notificación fue enviada correctamente a {$cantidad} dispositivos."
            );
        } else {
            session()->flash(
                'error',
                $resultado['error']
                    ?? 'La notificación fue guardada, pero no pudo enviarse.'
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
