<?php

namespace App\Patterns\Behavioral\State;

use App\Enums\StatusJogo;
use App\Models\Jogo;

/**
 * Padrão State — Context
 *
 * Resolve o estado concreto a partir do status atual do Jogo
 * e delega a transição solicitada ao estado correspondente.
 */
class JogoContext
{
    private JogoStateInterface $estadoAtual;

    public function __construct(private Jogo $jogo)
    {
        $this->estadoAtual = $this->resolverEstado($jogo->status);
    }

    /**
     * Mapeia o valor string (ou Enum) do status para o objeto de estado.
     */
    private function resolverEstado(string|StatusJogo $status): JogoStateInterface
    {
        $value = $status instanceof StatusJogo ? $status->value : $status;

        return match ($value) {
            StatusJogo::Aberto->value               => new JogoAberto(),
            StatusJogo::InscricoesEncerradas->value => new JogoInscricoesEncerradas(),
            StatusJogo::EmAndamento->value          => new JogoEmAndamento(),
            StatusJogo::Cancelado->value            => new JogoCancelado(),
            StatusJogo::Encerrado->value            => new JogoEncerrado(),
            default => throw new \InvalidArgumentException("Status desconhecido: {$value}"),
        };
    }

    public function abrir(): void              { $this->estadoAtual->abrir($this->jogo); }
    public function encerrarInscricoes(): void { $this->estadoAtual->encerrarInscricoes($this->jogo); }
    public function iniciar(): void            { $this->estadoAtual->iniciar($this->jogo); }
    public function cancelar(): void           { $this->estadoAtual->cancelar($this->jogo); }
    public function encerrar(): void           { $this->estadoAtual->encerrar($this->jogo); }
}
