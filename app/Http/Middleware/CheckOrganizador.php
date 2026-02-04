<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOrganizador
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está logado e se é admin OU organizador
        if (auth()->check() && in_array(auth()->user()->tipo, ['admin', 'organizador'])) {
            return $next($request);
        }

        // Se for apenas um jogador tentando acessar, manda de volta
        return redirect()->route('dashboard')->with('error', 'Acesso negado. Área exclusiva para organizadores e admins.');
    }
}