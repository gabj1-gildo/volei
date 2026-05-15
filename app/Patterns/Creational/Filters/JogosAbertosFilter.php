<?php

namespace App\Patterns\Creational\Filters;

use App\Models\Jogo;
use Illuminate\Database\Eloquent\Collection;

/**
 * Padrão Factory Method — Concreto (Jogador)
 *
 * Retorna apenas os jogos abertos e dentro do prazo de inscrição,
 * para exibição na vitrine do jogador.
 */
class JogosAbertosFilter extends JogoFilterFactory
{
    public function getJogos(): Collection
    {
        return Jogo::with(['titulo', 'local', 'responsavel'])
            ->withCount(['inscricoes' => fn ($q) => $q->whereNotIn('status', ['cancelada'])])
            ->whereNotIn('status', ['cancelado', 'encerrado'])
            ->where('data_hora_limite_inscricao', '>', now())
            ->get();
    }
}
