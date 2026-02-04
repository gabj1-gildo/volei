<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica se o usuário está logado e se é admin
        if (auth()->check() && auth()->user()->tipo === 'admin') {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Acesso negado. Apenas administradores podem gerenciar usuários.');
    }
}
