<?php

namespace App\Patterns\Behavioral\State;

use App\Enums\StatusJogo;
use App\Models\Jogo;

/**
 * Padrão State — Estado: Inscrições Encerradas
 *
 * Transições permitidas: iniciar(), cancelar()
 */
class JogoInscricoesEncerradas implements JogoStateInterface
{
    public function abrir(Jogo $jogo): void
    {
        // Permite reabrir caso necessário (ex.: cancelamento de erro)
        $jogo->update(['status' => StatusJogo::Aberto->value]);
    }

    public function encerrarInscricoes(Jogo $jogo): void
    {
        throw new \LogicException("As inscrições já estão encerradas.");
    }

    public function iniciar(Jogo $jogo): void
    {
        $jogo->update(['status' => StatusJogo::EmAndamento->value]);
    }

    public function cancelar(Jogo $jogo): void
    {
        $jogo->update(['status' => StatusJogo::Cancelado->value]);
    }

    public function encerrar(Jogo $jogo): void
    {
        throw new \LogicException("Inicie o jogo antes de encerrá-lo.");
    }
}
