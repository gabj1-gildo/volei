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
        $user = Auth::user();
        $jogos = Jogo::with(['titulo', 'local', 'responsavel'])
            ->withCount(['inscricoes' => function ($query) {
                $query->whereNotIn('status', ['cancelada']);
            }])
            ->with(['inscricoes' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->where('status', 'aberto')
            ->where('data_hora', '>', now())
            ->get();

        return view('jogos_disponiveis', compact('jogos'));
    }

    // Processa a inscrição do jogador no jogo
    public function inscrever(Request $request)
    {
        try {
            $jogoId = decrypt($request->jogo_id);
        } catch (\Exception $e) {
            return back()->with('error', 'ID do jogo inválido.');
        }

        // Busca se já existe uma inscrição (mesmo que cancelada)
        $inscricaoExistente = Inscricao::where('jogo_id', $jogoId)
                                        ->where('user_id', Auth::id())
                                        ->first();

        if ($inscricaoExistente) {
            // Se a inscrição estiver ativa (pendente ou aprovada), bloqueia
            if (in_array($inscricaoExistente->status, ['pendente', 'aprovada', 'confirmada'])) {
                return back()->with('error', 'Você já possui uma inscrição ativa para este jogo!');
            }

            // Se estiver cancelada ou recusada, nós atualizamos para 'pendente' novamente
            $inscricaoExistente->update(['status' => 'pendente']);
            
            return back()->with('success', 'Inscrição reiniciada! Aguarde nova aprovação.');
        }

        // Se não existir nenhuma, cria uma do zero
        Inscricao::create([
            'jogo_id' => $jogoId,
            'user_id' => Auth::id(),
            'status'  => 'pendente'
        ]);

        return back()->with('success', 'Inscrição realizada! Aguarde aprovação.');
    }

    public function cancelarInscricao(Request $request)
    {
        try {
            $id = decrypt($request->inscricao_id);
            $inscricao = Inscricao::where('id', $id)
                ->where('user_id', Auth::id()) // Segurança: só cancela a própria
                ->firstOrFail();

            $inscricao->update(['status' => 'cancelada']);

            return back()->with('success', 'Sua inscrição foi cancelada.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao cancelar inscrição.');
        }
    }
    public function dashboard()
    {
        // 1. Busca os jogos disponíveis para a vitrine
        $jogos = Jogo::with(['titulo', 'local'])
            ->withCount(['inscricoes' => function ($query) {
                $query->whereNotIn('status', ['cancelada']);
            }])
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
