<?php

namespace App\Patterns\Behavioral\State;

use App\Enums\StatusJogo;
use App\Models\Jogo;

/**
 * Padrão State — Estado: Em Andamento
 *
 * Transições permitidas: encerrar(), cancelar()
 */
class JogoEmAndamento implements JogoStateInterface
{
    public function abrir(Jogo $jogo): void
    {
        throw new \LogicException("Não é possível reabrir um jogo em andamento.");
    }

    public function encerrarInscricoes(Jogo $jogo): void
    {
        throw new \LogicException("O jogo já está em andamento; inscrições não estão mais disponíveis.");
    }

    public function iniciar(Jogo $jogo): void
    {
        throw new \LogicException("O jogo já está em andamento.");
    }

    public function cancelar(Jogo $jogo): void
    {
        $jogo->update(['status' => StatusJogo::Cancelado->value]);
    }

    public function encerrar(Jogo $jogo): void
    {
        $jogo->update(['status' => StatusJogo::Encerrado->value]);
    }
}
