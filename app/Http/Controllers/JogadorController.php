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
    public function inscrever(Request $request) {
        // Verifica se já está inscrito
        $existe = Inscricao::where('jogo_id', $request->jogo_id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($existe) {
            return back()->with('error', 'Você já está inscrito nesta partida!');
        }

        Inscricao::create([
            'jogo_id' => $request->jogo_id,
            'user_id' => Auth::id(),
            'status' => 'pendente'
        ]);

        return back()->with('success', 'Inscrição realizada! Aguarde a aprovação do organizador.');
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
