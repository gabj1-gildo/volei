<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Patterns\Behavioral\Strategy\AuthStrategyInterface;
use App\Patterns\Behavioral\Strategy\EmailPasswordStrategy;
use App\Patterns\Creational\SessionManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * Padrão Strategy: a autenticação é delegada a EmailPasswordStrategy,
     * permitindo troca futura por OAuth, LDAP, 2FA sem alterar este controller.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Strategy: encapsula o algoritmo de autenticação
        /** @var AuthStrategyInterface $strategy */
        $strategy = new EmailPasswordStrategy();

        if (! $strategy->autenticar($request->only('email', 'password'))) {
            // Fallback para o fluxo padrão do Breeze (que lança ValidationException)
            $request->authenticate();
        }

        $request->session()->regenerate();

        // Singleton: registra acesso bem-sucedido
        $session = SessionManager::getInstance();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
