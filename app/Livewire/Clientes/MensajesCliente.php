<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Models\MensajeCliente as Mensaje;
use Livewire\Component;

class MensajesCliente extends Component
{
    public Cliente $cliente;

    public string $mensaje = '';

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;

        $this->marcarMensajesComoLeidos();
    }

    public function enviarMensaje(): void
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

        $user = auth()->user();

        if ($user->role === 'cliente') {
            $this->cliente->chat_borrado_cliente_at = now();
        } elseif ($user->role === 'abogado') {
            $this->cliente->chat_borrado_abogado_at = now();
        } else {
            abort(403);
        }

        $this->cliente->save();
        $this->cliente->refresh();
    }

    public function marcarMensajesComoLeidos(): void
    {
        $this->validarAcceso();

        $remitentePendiente = auth()->user()->role === 'cliente'
            ? 'estudio'
            : 'cliente';

        $query = $this->cliente
            ->mensajes()
            ->where('remitente', $remitentePendiente)
            ->where('leido', false);

        /*
         * Solo marca como leídos los mensajes que el usuario
         * todavía puede ver después de haber limpiado su chat.
         */
        if (
            auth()->user()->role === 'cliente'
            && $this->cliente->chat_borrado_cliente_at
        ) {
            $query->where(
                'created_at',
                '>',
                $this->cliente->chat_borrado_cliente_at
            );
        }

        if (
            auth()->user()->role === 'abogado'
            && $this->cliente->chat_borrado_abogado_at
        ) {
            $query->where(
                'created_at',
                '>',
                $this->cliente->chat_borrado_abogado_at
            );
        }

        $query->update([
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

        $query = $this->cliente
            ->mensajes()
            ->oldest();

        $user = auth()->user();

        if (
            $user->role === 'cliente'
            && $this->cliente->chat_borrado_cliente_at
        ) {
            $query->where(
                'created_at',
                '>',
                $this->cliente->chat_borrado_cliente_at
            );
        }

        if (
            $user->role === 'abogado'
            && $this->cliente->chat_borrado_abogado_at
        ) {
            $query->where(
                'created_at',
                '>',
                $this->cliente->chat_borrado_abogado_at
            );
        }

        $mensajes = $query->get();

        return view('livewire.clientes.mensajes-cliente', [
            'mensajes' => $mensajes,
        ]);
    }
}

