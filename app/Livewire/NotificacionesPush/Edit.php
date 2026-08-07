<?php

namespace App\Livewire\NotificacionesPush;

use App\Models\Cliente;
use App\Models\NotificacionPush;
use Livewire\Component;

class Edit extends Component
{
    public NotificacionPush $notificacion;

    public string $titulo = '';

    public string $mensaje = '';

    public string $destinatario = 'todos';

    public ?int $clienteId = null;

    public string $pantalla = 'inicio';

    public ?string $programadaPara = null;

    public function mount(NotificacionPush $notificacion): void
    {
        abort_unless(
            $notificacion->user_id === auth()->id(),
            403
        );

        abort_if(
            $notificacion->estado !== 'pendiente',
            403,
            'Solo pueden editarse las notificaciones pendientes.'
        );

        $this->notificacion = $notificacion;

        $this->titulo = $notificacion->titulo;
        $this->mensaje = $notificacion->mensaje;
        $this->destinatario = $notificacion->destinatario;
        $this->clienteId = $notificacion->cliente_id;
        $this->pantalla = $notificacion->pantalla;
        $this->programadaPara = optional($notificacion->programada_para)
            ?->format('Y-m-d\TH:i');
    }

    protected function rules(): array
    {
        return [
            'titulo' => ['required', 'max:255'],
            'mensaje' => ['required', 'max:2000'],
            'destinatario' => ['required'],
            'clienteId' => ['nullable'],
            'pantalla' => ['required'],
            'programadaPara' => ['nullable', 'date'],
        ];
    }

    public function guardar(): void
    {
        $this->validate();

        $this->notificacion->update([
            'titulo' => trim($this->titulo),
            'mensaje' => trim($this->mensaje),
            'destinatario' => $this->destinatario,
            'cliente_id' => $this->clienteId,
            'pantalla' => $this->pantalla,
            'programada_para' => $this->programadaPara,
        ]);

        session()->flash(
            'success',
            'La notificación fue actualizada correctamente.'
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
            'livewire.notificaciones-push.edit',
            [
                'clientes' => $clientes,
            ]
        );
    }
}
