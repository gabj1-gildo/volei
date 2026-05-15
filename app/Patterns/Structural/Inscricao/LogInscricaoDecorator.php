<?php

namespace App\Patterns\Structural\Inscricao;

use App\Enums\StatusInscricao;
use App\Models\Inscricao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Padrão Decorator — Concreto com Auditoria
 *
 * Envolve qualquer InscricaoHandlerInterface e acrescenta
 * registro de auditoria via Log::info() antes e após a alteração.
 * O handler interno é chamado sem modificação (Open/Closed Principle).
 */
class LogInscricaoDecorator implements InscricaoHandlerInterface
{
    public function __construct(
        private InscricaoHandlerInterface $handler
    ) {}

    public function alterarStatus(Inscricao $inscricao, StatusInscricao $novoStatus): void
    {
        $statusAnterior = $inscricao->status;

        // Delega a persistência ao handler interno
        $this->handler->alterarStatus($inscricao, $novoStatus);

        // Registra auditoria após a persistência
        Log::info('[INSCRIÇÃO] Status alterado', [
            'inscricao_id'    => $inscricao->id,
            'jogo_id'         => $inscricao->jogo_id,
            'usuario_id'      => $inscricao->user_id,
            'usuario_nome'    => $inscricao->user?->name,
            'status_anterior' => $statusAnterior,
            'novo_status'     => $novoStatus->value,
            'alterado_por_id' => Auth::id(),
            'alterado_por'    => Auth::user()?->name,
            'timestamp'       => now()->toDateTimeString(),
        ]);
    }
}
