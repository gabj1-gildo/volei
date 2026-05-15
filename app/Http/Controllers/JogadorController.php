<?php

namespace App\Http\Controllers;

use App\Enums\StatusInscricao;
use App\Models\Inscricao;
use App\Models\Jogo;
use App\Patterns\Creational\Filters\JogoFilterFactory;
use App\Patterns\Creational\SessionManager;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JogadorController extends Controller
{
    /**
     * Vitrine de jogos disponíveis para inscrição.
     *
     * Padrão Factory Method: JogosAbertosFilter filtra os jogos corretos para o jogador.
     * Padrão Singleton: SessionManager provê o usuário logado.
     */
    public function index(): View
    {
        $session = SessionManager::getInstance();
        $user    = $session->getUser();

        // Factory Method: jogador usa JogosAbertosFilter
        $jogos = JogoFilterFactory::resolverFiltro('jogador')
            ->getJogos()
            ->each(function ($jogo) use ($user) {
                // Carrega a inscrição do usuário atual para cada jogo
                $jogo->minhaInscricao = $jogo->inscricoes
                    ->where('user_id', $user->id)
                    ->first();
            });

        return view('jogos_disponiveis', compact('jogos'));
    }

    /**
     * Dashboard do jogador com vitrine e suas inscrições.
     *
     * Padrão Singleton: acesso centralizado ao ID do usuário logado.
     */
    public function dashboard(): View
    {
        $session = SessionManager::getInstance();

        $jogos = Jogo::with(['titulo', 'local'])
            ->withCount(['inscricoes' => fn ($q) => $q->whereNotIn('status', [StatusInscricao::Cancelada->value])])
            ->where('status', 'aberto')
            ->get();

        $minhasInscricoes = Inscricao::where('user_id', $session->getUserId())
            ->with('jogo.titulo')
            ->get();

        return view('dashboard', compact('jogos', 'minhasInscricoes'));
    }

    /**
     * Inscreve o jogador em um jogo.
     *
     * Padrão Singleton: ID do usuário via SessionManager.
     */
    public function inscrever(Request $request): RedirectResponse
    {
        try {
            $jogoId = decrypt($request->jogo_id);
            $jogo   = Jogo::withCount(['inscricoes' => fn ($q) =>
                $q->whereIn('status', [StatusInscricao::Pendente->value, StatusInscricao::Confirmado->value])
            ])->findOrFail($jogoId);
        } catch (\Exception $e) {
            return back()->with('error', 'Jogo não encontrado ou link inválido.');
        }

        if (Carbon::now()->gt(Carbon::parse($jogo->data_hora_limite_inscricao))) {
            return back()->with('error', 'O prazo para inscrições já encerrou.');
        }

        if ($jogo->inscricoes_count >= $jogo->limite_jogadores) {
            return back()->with('error', 'Este jogo já atingiu o limite máximo de jogadores.');
        }

        $session = SessionManager::getInstance();

        $inscricao = Inscricao::updateOrCreate(
            ['jogo_id' => $jogoId, 'user_id' => $session->getUserId()],
            ['status'  => StatusInscricao::Pendente->value]
        );

        $mensagem = $inscricao->wasRecentlyCreated
            ? 'Inscrição realizada! Aguarde aprovação.'
            : 'Inscrição reiniciada! Aguarde nova aprovação.';

        return back()->with('success', $mensagem);
    }

    /**
     * Cancela a inscrição do próprio jogador.
     *
     * Padrão Singleton: ID do usuário via SessionManager.
     */
    public function cancelarInscricao(Request $request): RedirectResponse
    {
        try {
            $id        = decrypt($request->inscricao_id);
            $session   = SessionManager::getInstance();

            $inscricao = Inscricao::where('id', $id)
                ->where('user_id', $session->getUserId()) // segurança: só cancela a própria
                ->firstOrFail();

            $inscricao->update(['status' => StatusInscricao::Cancelada->value]);

            return back()->with('success', 'Sua inscrição foi cancelada.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao cancelar inscrição.');
        }
    }

    /**
     * Lista todas as inscrições do jogador logado.
     *
     * Padrão Singleton: ID do usuário via SessionManager.
     */
    public function minhasInscricoes(): View
    {
        $session = SessionManager::getInstance();

        $inscricoes = Inscricao::with(['jogo.titulo', 'jogo.local'])
            ->where('user_id', $session->getUserId())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('minhas_inscricoes', compact('inscricoes'));
    }
}
