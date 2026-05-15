<?php

namespace App\Patterns\Behavioral\State;

use App\Enums\StatusJogo;
use App\Models\Jogo;

/**
 * Padrão State — Estado: Aberto
 *
 * Transições permitidas: encerrarInscricoes(), cancelar()
 */
class JogoAberto implements JogoStateInterface
{
    public function abrir(Jogo $jogo): void
    {
        throw new \LogicException("O jogo já está aberto.");
    }

    public function encerrarInscricoes(Jogo $jogo): void
    {
        $jogo->update(['status' => StatusJogo::InscricoesEncerradas->value]);
    }

    public function iniciar(Jogo $jogo): void
    {
        throw new \LogicException("Encerre as inscrições antes de iniciar o jogo.");
    }

    public function cancelar(Jogo $jogo): void
    {
        $jogo->update(['status' => StatusJogo::Cancelado->value]);
    }

    public function encerrar(Jogo $jogo): void
    {
        throw new \LogicException("Não é possível encerrar um jogo que ainda está aberto.");
    }
}
