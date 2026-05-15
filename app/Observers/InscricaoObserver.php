<?php

namespace App\Observers;

use App\Enums\StatusInscricao;
use App\Models\Inscricao;
use Illuminate\Support\Facades\Log;

/**
 * Padrão Observer
 *
 * Reage automaticamente ao evento 'saved' do Model Inscricao.
 * Recalcula e persiste o campo 'vagas_disponiveis' no Jogo relacionado,
 * desacoplando esta responsabilidade dos controllers.
 */
class InscricaoObserver
{
    /**
     * Disparado após qualquer create() ou update() em Inscricao.
     */
    public function saved(Inscricao $inscricao): void
    {
        $this->recalcularVagas($inscricao);
    }

    /**
     * Disparado após qualquer delete() em Inscricao.
     */
    public function deleted(Inscricao $inscricao): void
    {
        $this->recalcularVagas($inscricao);
    }

    /**
     * Conta inscrições ativas e atualiza o campo vagas_disponiveis no Jogo.
     */
    private function recalcularVagas(Inscricao $inscricao): void
    {
        $jogo = $inscricao->jogo;

        if ($jogo === null) {
            return;
        }

        // Status que ocupam vaga: pendente e confirmado
        $statusQueOcupam = array_filter(
            StatusInscricao::cases(),
            fn (StatusInscricao $s) => $s->ocupaVaga()
        );
        $valoresOcupam = array_map(fn ($s) => $s->value, $statusQueOcupam);

        $inscricoesAtivas = $jogo->inscricoes()
            ->whereIn('status', $valoresOcupam)
            ->count();

        $vagasDisponiveis = max(0, $jogo->limite_jogadores - $inscricoesAtivas);

        // Usa updateQuietly para evitar loop recursivo de eventos
        $jogo->updateQuietly(['vagas_disponiveis' => $vagasDisponiveis]);

        Log::debug('[OBSERVER] Vagas recalculadas', [
            'jogo_id'           => $jogo->id,
            'limite_jogadores'  => $jogo->limite_jogadores,
            'inscricoes_ativas' => $inscricoesAtivas,
            'vagas_disponiveis' => $vagasDisponiveis,
        ]);
    }
}
