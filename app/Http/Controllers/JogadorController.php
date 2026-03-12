<?php

namespace App\Http\Controllers;

use App\Models\Jogo;
use App\Models\Inscricao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            ->whereNotIn('status', ['cancelado', 'encerrado'])
            ->where('data_hora', '>', now())
            ->get();

        return view('jogos_disponiveis', compact('jogos'));
    }

    public function inscrever(Request $request)
    {
        try {
            $jogoId = decrypt($request->jogo_id);
            $jogo = Jogo::withCount(['inscricoes' => function($q) {
                $q->whereIn('status', ['pendente', 'confirmada','cancelada']);
            }])->findOrFail($jogoId);
        } catch (\Exception $e) {
            return back()->with('error', 'Jogo não encontrado ou link inválido.');
        }

        // 1. Validação de Horário (O coração da sua pergunta)
        if (Carbon::now()->gt(Carbon::parse($jogo->data_hora_limite_inscricao))) {
            return back()->with('error', 'O prazo para inscrições já encerrou.');
        }

        // 2. Validação de Limite de Jogadores
        if ($jogo->inscricoes_count >= $jogo->limite_jogadores) {
            return back()->with('error', 'Este jogo já atingiu o limite máximo de jogadores.');
        }

        // 3. Lógica de Inscrição (Limpa e Reutilizável)
        $inscricao = Inscricao::updateOrCreate(
            ['jogo_id' => $jogoId, 'user_id' => auth()->id()],
            ['status' => 'pendente']
        );

        // Determina a mensagem com base se foi uma criação nova ou atualização
        $mensagem = $inscricao->wasRecentlyCreated 
            ? 'Inscrição realizada! Aguarde aprovação.' 
            : 'Inscrição reiniciada! Aguarde nova aprovação.';

        return back()->with('success', $mensagem);
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

    public function minhasInscricoes()
    {
        // Busca as inscrições do usuário logado com os dados dos jogos relacionados
        $inscricoes = Inscricao::with(['jogo.titulo', 'jogo.local'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('minhas_inscricoes', compact('inscricoes'));
    }
}
