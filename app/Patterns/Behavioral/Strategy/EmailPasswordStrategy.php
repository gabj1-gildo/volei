<?php

namespace App\Patterns\Behavioral\Strategy;

use Illuminate\Support\Facades\Auth;

/**
 * Padrão Strategy — Concreto: E-mail + Senha
 *
 * Implementa autenticação padrão via Auth::attempt() do Laravel.
 * Pode ser substituída por outra implementação de AuthStrategyInterface
 * sem alterar o AuthenticatedSessionController.
 */
class EmailPasswordStrategy implements AuthStrategyInterface
{
    /**
     * Autentica o usuário pelo e-mail e senha informados.
     *
     * @param  array{email: string, password: string} $credentials
     */
    public function autenticar(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }
}
