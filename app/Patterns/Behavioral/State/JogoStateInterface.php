<?php

namespace App\Patterns\Behavioral\State;

use App\Models\Jogo;

/**
 * Padrão State — Interface
 *
 * Define o contrato de transições de estado de um Jogo.
 * Cada estado concreto implementa apenas as transições válidas
 * a partir dele; as demais lançam \LogicException.
 */
interface JogoStateInterface
{
    /**
     * Reabre o jogo para inscrições.
     */
    public function abrir(Jogo $jogo): void;

    /**
     * Encerra o período de inscrições (mas o jogo ainda não começou).
     */
    public function encerrarInscricoes(Jogo $jogo): void;

    /**
     * Marca o jogo como em andamento.
     */
    public function iniciar(Jogo $jogo): void;

    /**
     * Cancela o jogo (estado terminal).
     */
    public function cancelar(Jogo $jogo): void;

    /**
     * Encerra o jogo (estado terminal, após conclusão).
     */
    public function encerrar(Jogo $jogo): void;
}
