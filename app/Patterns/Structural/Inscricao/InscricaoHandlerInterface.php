<?php

namespace App\Patterns\Structural\Inscricao;

use App\Enums\StatusInscricao;
use App\Models\Inscricao;

/**
 * Padrão Decorator — Interface
 *
 * Contrato para handlers de alteração de status de inscrição.
 * Permite encadear comportamentos (ex.: persistência + log) sem herança.
 */
interface InscricaoHandlerInterface
{
    /**
     * Altera o status da inscrição para o novo valor informado.
     */
    public function alterarStatus(Inscricao $inscricao, StatusInscricao $novoStatus): void;
}
