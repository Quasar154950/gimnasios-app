<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Models\MensajeCliente as Mensaje;
use Livewire\Component;

class MensajesCliente extends Component
{
    public Cliente $cliente;

    public string $mensaje = '';

    public function mount(Cliente $cliente)
    {
        $this->cliente = $cliente;

        $this->marcarMensajesComoLeidos();
    }

    public function enviarMensaje()
    {
        $this->validarAcceso();

        $this->validate([
            'mensaje' => 'required|min:2',
        ]);

        Mensaje::create([
            'cliente_id' => $this->cliente->id,
            'user_id' => auth()->id(),
            'mensaje' => $this->mensaje,
            'remitente' => auth()->user()->role === 'cliente'
                ? 'cliente'
                : 'estudio',
        ]);

        $this->mensaje = '';
    }

    public function vaciarConversacion(): void
    {
    $this->validarAcceso();

    $this->cliente->mensajes()->delete();
    }

    public function marcarMensajesComoLeidos(): void
    {
        $this->validarAcceso();

        $remitentePendiente = auth()->user()->role === 'cliente'
            ? 'estudio'
            : 'cliente';

        $this->cliente
            ->mensajes()
            ->where('remitente', $remitentePendiente)
            ->where('leido', false)
            ->update([
                'leido' => true,
                'leido_at' => now(),
            ]);
    }

    private function validarAcceso(): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        if ($user->role === 'abogado') {
            if ((int) $this->cliente->abogado_id !== (int) $user->id) {
                abort(403);
            }

            return;
        }

        if ($user->role === 'cliente') {
            if ((int) $this->cliente->user_id !== (int) $user->id) {
                abort(403);
            }

            return;
        }

        abort(403);
    }

    public function render()
    {
        $this->marcarMensajesComoLeidos();

        $mensajes = $this->cliente
            ->mensajes()
            ->oldest()
            ->get();

        return view('livewire.clientes.mensajes-cliente', [
            'mensajes' => $mensajes,
        ]);
    }
}