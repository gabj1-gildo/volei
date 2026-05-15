<?php

namespace App\Patterns\Creational\Filters;

use App\Models\Jogo;
use Illuminate\Database\Eloquent\Collection;

/**
 * Padrão Factory Method — Concreto (Admin/Organizador)
 *
 * Retorna todos os jogos (independente de status), agrupados por responsável,
 * para uso no painel de gestão.
 */
class TodosJogosFilter extends JogoFilterFactory
{
    public function getJogos(): Collection
    {
        return Jogo::with(['titulo', 'local', 'responsavel'])
            ->withCount(['inscricoes' => fn ($q) => $q->whereNotIn('status', ['cancelada'])])
            ->get();
    }
}
