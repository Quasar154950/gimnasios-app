<?php

namespace App\Livewire\NotificacionesPush;

use App\Models\Cliente;
use App\Models\NotificacionPush;
use Livewire\Component;

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

    public function guardar(): void
    {
        $this->validate();

        if ($this->destinatario === 'cliente' && ! $this->clienteId) {
            $this->addError(
                'clienteId',
                'Seleccioná el socio que recibirá la notificación.'
            );

            return;
        }

        if ($this->modoEnvio === 'programar' && ! $this->programadaPara) {
            $this->addError(
                'programadaPara',
                'Seleccioná la fecha y hora de envío.'
            );

            return;
        }

        if ($this->clienteId) {
            Cliente::query()
                ->where('abogado_id', auth()->id())
                ->findOrFail($this->clienteId);
        }

        NotificacionPush::create([
            'user_id' => auth()->id(),
            'cliente_id' => $this->clienteId,
            'titulo' => trim($this->titulo),
            'mensaje' => trim($this->mensaje),
            'tipo' => 'manual',
            'destinatario' => $this->destinatario,
            'estado' => $this->modoEnvio === 'programar'
                ? 'pendiente'
                : 'borrador',
            'programada_para' => $this->modoEnvio === 'programar'
                ? $this->programadaPara
                : null,
            'cantidad_enviada' => 0,
        ]);

        session()->flash(
            'success',
            'La notificación fue guardada correctamente.'
        );

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
