<?php

namespace App\Patterns\Creational;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Padrão Singleton
 *
 * Centraliza o acesso à sessão do usuário autenticado em um único ponto.
 * Evita chamadas diretas a auth()->user() e Auth::id() espalhadas pelos controllers,
 * facilitando a substituição da fonte de autenticação no futuro.
 */
final class SessionManager
{
    private static ?self $instance = null;

    /**
     * Construtor privado impede instanciação externa.
     */
    private function __construct() {}

    /**
     * Ponto de acesso global à instância única.
     */
    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Clonagem proibida (Singleton).
     */
    private function __clone() {}

    /**
     * Deserialização proibida (Singleton).
     */
    public function __wakeup(): never
    {
        throw new \RuntimeException('SessionManager não pode ser deserializado.');
    }

    // ─────────────────────────────────────────────────────────────
    //  Métodos de acesso à sessão
    // ─────────────────────────────────────────────────────────────

    /**
     * Retorna o usuário autenticado ou null se não houver sessão.
     */
    public function getUser(): ?User
    {
        /** @var User|null */
        return Auth::user();
    }

    /**
     * Retorna o ID do usuário autenticado ou null.
     */
    public function getUserId(): ?int
    {
        return Auth::id();
    }

    /**
     * Retorna o tipo/role do usuário autenticado.
     */
    public function getUserTipo(): ?string
    {
        return Auth::user()?->tipo;
    }

    /**
     * Verifica se o usuário tem determinado tipo.
     */
    public function userIs(string ...$tipos): bool
    {
        return in_array($this->getUserTipo(), $tipos, strict: true);
    }

    /**
     * Verifica se existe uma sessão ativa.
     */
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }
}
