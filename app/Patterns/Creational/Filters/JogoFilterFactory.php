<?php

namespace App\Patterns\Creational\Filters;

use Illuminate\Database\Eloquent\Collection;

/**
 * Padrão Factory Method — Classe Abstrata
 *
 * Define o contrato para os filtros de listagem de jogos
 * e o factory method estático que resolve qual filtro usar
 * com base no tipo do usuário.
 */
abstract class JogoFilterFactory
{
    /**
     * Retorna a coleção de jogos de acordo com o filtro concreto.
     */
    abstract public function getJogos(): Collection;

    /**
     * Factory Method: decide qual filtro instanciar com base no perfil do usuário.
     *
     * @param  string $userTipo  'admin' | 'organizador' | 'jogador'
     */
    public static function resolverFiltro(string $userTipo): static
    {
        return match ($userTipo) {
            'admin', 'organizador' => new TodosJogosFilter(),
            default                => new JogosAbertosFilter(),
        };
    }
}
