<?php

namespace App\Http\Controllers;

use App\Models\Jogo;
use App\Models\Inscricao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JogadorController extends Controller
{
    // Lista todos os jogos abertos para o jogador se inscrever
    public function index() {
        $jogos = Jogo::with(['titulo', 'local', 'responsavel'])
            ->where('status', 'aberto')
            ->where('data_hora', '>', now())
            ->get();

        return view('jogos_disponiveis', compact('jogos'));
    }

    // Processa a inscrição do jogador no jogo
    public function inscrever(Request $request)
    {
        try {
            // 1. Descriptografamos o ID que veio do formulário
            $jogoId = decrypt($request->jogo_id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Caso o ID seja inválido ou não esteja criptografado
            return back()->with('error', 'ID do jogo inválido.');
        }

        // 2. Agora verificamos se o usuário já está inscrito usando o ID real (número)
        $existe = Inscricao::where('jogo_id', $jogoId)
                        ->where('user_id', Auth::id())
                        ->exists();

        if ($existe) {
            return back()->with('error', 'Você já está inscrito nesta partida!');
        }

        // 3. Salvamos usando o ID descriptografado
        Inscricao::create([
            'jogo_id' => $jogoId,
            'user_id' => Auth::id(),
            'status'  => 'pendente'
        ]);

        return back()->with('success', 'Inscrição realizada! Aguarde aprovação.');
    }

    public function dashboard()
    {
        // 1. Busca os jogos disponíveis para a vitrine
        $jogos = Jogo::with(['titulo', 'local'])
            ->where('status', 'aberto')
            ->get();

        // 2. Busca as inscrições do usuário logado
        $minhasInscricoes = Inscricao::where('user_id', Auth::id())
            ->with('jogo.titulo')
            ->get();

        // 3. Passa as variáveis para a view
        return view('dashboard', compact('jogos', 'minhasInscricoes'));
    }
}
