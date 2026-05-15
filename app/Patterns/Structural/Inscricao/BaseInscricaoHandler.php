<?php

namespace App\Patterns\Structural\Inscricao;

use App\Enums\StatusInscricao;
use App\Models\Inscricao;

/**
 * Padrão Decorator — Componente Concreto (Base)
 *
 * Implementação base que apenas persiste o novo status no banco.
 * Pode ser decorado com comportamentos adicionais (ex.: log, notificação).
 */
class BaseInscricaoHandler implements InscricaoHandlerInterface
{
    public function alterarStatus(Inscricao $inscricao, StatusInscricao $novoStatus): void
    {
        $inscricao->update(['status' => $novoStatus->value]);
    }
}
