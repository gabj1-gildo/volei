<?php

namespace App\Patterns\Behavioral\Strategy;

/**
 * Padrão Strategy — Interface
 *
 * Define o contrato de autenticação.
 * Permite trocar o algoritmo de autenticação sem modificar o controller
 * (ex.: substituir por OAuth, two-factor, LDAP, etc.).
 */
interface AuthStrategyInterface
{
    /**
     * Autentica o usuário com as credenciais fornecidas.
     *
     * @param  array<string, string> $credentials
     * @return bool  true se autenticado com sucesso
     */
    public function autenticar(array $credentials): bool;
}
