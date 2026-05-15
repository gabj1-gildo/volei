<?php

namespace App\Patterns\Behavioral\State;

use App\Models\Jogo;

/**
 * Padrão State — Estado: Encerrado (Terminal)
 *
 * Nenhuma transição é permitida a partir deste estado.
 */
class JogoEncerrado implements JogoStateInterface
{
    private function negar(string $acao): never
    {
        throw new \LogicException("Não é possível {$acao} um jogo já encerrado.");
    }

    public function abrir(Jogo $jogo): void              { $this->negar('reabrir'); }
    public function encerrarInscricoes(Jogo $jogo): void { $this->negar('encerrar inscrições de'); }
    public function iniciar(Jogo $jogo): void             { $this->negar('iniciar'); }
    public function cancelar(Jogo $jogo): void            { $this->negar('cancelar'); }
    public function encerrar(Jogo $jogo): void            { throw new \LogicException("O jogo já está encerrado."); }
}
