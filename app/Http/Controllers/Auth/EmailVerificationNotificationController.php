<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard');
        }

        // Tenta enviar e captura o erro se falhar
        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            // ISSO VAI PARAR O CÓDIGO E MOSTRAR O ERRO EXATO NA TELA
            dd([
                'ERRO' => $e->getMessage(),
                'CONFIG_MAILER' => config('mail.default'),
                'CONFIG_HOST' => config('mail.mailers.smtp.host'),
                'CONFIG_PORT' => config('mail.mailers.smtp.port'),
                'CONFIG_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            ]);
        }

        // Alterei de JSON para back() para voltar para a página corretamente
        return back()->with('status', 'verification-link-sent');
    }
}
