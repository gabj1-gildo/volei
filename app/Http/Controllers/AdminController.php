<?php
namespace App\Http\Controllers;

use App\Enums\StatusInscricao;
use App\Enums\StatusJogo;
use App\Http\Requests\AtualizarJogoRequest;
use App\Http\Requests\SalvarJogoRequest;
use App\Http\Requests\SalvarLocalRequest;
use App\Http\Requests\SalvarTituloRequest;
use App\Models\Inscricao;
use App\Models\Jogo;
use App\Models\Local;
use App\Models\Titulo;
use App\Models\User;
use App\Patterns\Creational\Filters\JogoFilterFactory;
use App\Patterns\Creational\SessionManager;
use App\Patterns\Structural\Inscricao\BaseInscricaoHandler;
use App\Patterns\Structural\Inscricao\LogInscricaoDecorator;
use App\Patterns\Structural\JogoFacade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  Gerenciar Jogos
    // ─────────────────────────────────────────────────────────────

    /**
     * Padrão Factory Method: o filtro correto é resolvido pelo tipo do usuário.
     * Padrão Singleton: acesso ao tipo do usuário via SessionManager.
     */
    public function gerenciarJogos(): View
    {
        $session = SessionManager::getInstance();

        // Factory Method: admin/organizador usam TodosJogosFilter
        $jogosAgrupados = JogoFilterFactory::resolverFiltro($session->getUserTipo())
            ->getJogos()
            ->groupBy(fn ($j) => $j->responsavel?->name ?? 'Sem Responsável');

        $titulos      = Titulo::where('ativo', true)->get();
        $locais       = Local::where('ativo', true)->get();
        $organizadores = User::where('tipo', 'organizador')->get();

        return view('admin_gerenciar_jogos', compact('jogosAgrupados', 'titulos', 'locais', 'organizadores'));
    }

    /**
     * Padrão Facade + Builder: criação do jogo orquestrada pelo JogoFacade.
     * Padrão Form Request: validação e autorização em SalvarJogoRequest.
     */
    public function salvarJogo(SalvarJogoRequest $request): RedirectResponse
    {
        try {
            // Facade orquestra Builder + SessionManager internamente
            JogoFacade::criarJogo($request);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['data' => $e->getMessage()])->withInput();
        }

        return redirect()->route('gerenciar_jogos')->with('success', 'Jogo criado com sucesso!');
    }

    /**
     * Padrão Facade: atualização delegada ao JogoFacade.
     * Padrão Form Request: validação em AtualizarJogoRequest.
     */
    public function atualizarJogo(AtualizarJogoRequest $request): RedirectResponse
    {
        $jogo = Jogo::findOrFail($request->id);

        try {
            JogoFacade::atualizarJogo($request, $jogo);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['data' => $e->getMessage()])->withInput();
        }

        return back()->with('success', 'Jogo atualizado com sucesso!');
    }

    /**
     * Padrão State (via Facade): transição de status validada pelo State Pattern.
     * Transições inválidas são capturadas e exibidas como mensagem de erro.
     */
    public function alterarStatusJogo(Request $request): RedirectResponse
    {
        $request->validate([
            'id_partida' => 'required|exists:jogos,id',
            'status'     => 'required|string|in:' . implode(',', StatusJogo::values()),
        ]);

        $jogo = Jogo::findOrFail($request->id_partida);

        try {
            JogoFacade::alterarStatus($jogo, $request->status);
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Status da partida atualizado!');
    }

    // ─────────────────────────────────────────────────────────────
    //  Gerenciar Inscrições
    // ─────────────────────────────────────────────────────────────

    public function gerenciarInscricoes(Request $request): View
    {
        $query = Jogo::with(['inscricoes.user', 'titulo', 'local'])
            ->where('status', StatusJogo::Aberto->value);

        if ($request->jogo) {
            $query->where('id', $request->jogo);
        }

        $jogos = $query->get();

        return view('admin_gerenciar_inscricoes', compact('jogos'));
    }

    /**
     * Padrão Decorator: BaseInscricaoHandler + LogInscricaoDecorator.
     * O log de auditoria é adicionado sem modificar a lógica de persistência.
     */
    public function alterarStatusInscricao(Request $request): RedirectResponse
    {
        $request->validate([
            'id_inscricao' => 'required|exists:inscricoes,id',
            'status'       => 'required|in:' . implode(',', StatusInscricao::values()),
        ]);

        $inscricao   = Inscricao::with('user')->findOrFail($request->id_inscricao);
        $novoStatus  = StatusInscricao::from($request->status);

        // Decorator: persiste + registra log de auditoria
        $handler = new LogInscricaoDecorator(new BaseInscricaoHandler());
        $handler->alterarStatus($inscricao, $novoStatus);

        return back()->with('success', 'Status atualizado!');
    }

    // ─────────────────────────────────────────────────────────────
    //  Gerenciar Usuários
    // ─────────────────────────────────────────────────────────────

    public function gerenciarUsuarios(): View
    {
        $usuarios = User::orderBy('name', 'asc')->get();
        return view('admin_gerenciar_usuarios', compact('usuarios'));
    }

    public function atualizarTipoUsuario(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipo'    => 'required|in:admin,organizador,jogador',
        ]);

        // Singleton: acessa usuário logado de forma centralizada
        $session   = SessionManager::getInstance();
        $userAlvo  = User::findOrFail($request->user_id);

        if ($userAlvo->id === $session->getUserId()) {
            return back()->with('error', 'Você não pode alterar seu próprio nível de acesso.');
        }

        if ($userAlvo->tipo === 'admin') {
            return back()->with('error', 'Segurança: Um administrador não pode alterar o cargo de outro administrador.');
        }

        $userAlvo->update(['tipo' => $request->tipo]);

        return back()->with('success', "O nível de acesso de {$userAlvo->name} foi atualizado!");
    }

    // ─────────────────────────────────────────────────────────────
    //  Gerenciar Títulos
    // ─────────────────────────────────────────────────────────────

    public function gerenciarTitulos(): View
    {
        $titulos = Titulo::orderBy('nome', 'asc')->get();
        return view('admin_gerenciar_titulos', compact('titulos'));
    }

    /**
     * Padrão Form Request: validação e autorização em SalvarTituloRequest.
     */
    public function salvarTitulo(SalvarTituloRequest $request): RedirectResponse
    {
        Titulo::create($request->only('nome'));
        return redirect()->route('gerenciar_titulos')->with('success', 'Título criado com sucesso!');
    }

    public function atualizarTitulo(SalvarTituloRequest $request): RedirectResponse
    {
        $titulo = Titulo::findOrFail($request->id);
        $titulo->update($request->only('nome'));
        return redirect()->route('gerenciar_titulos')->with('success', 'Título atualizado com sucesso!');
    }

    public function alternarStatusTitulo(Request $request): RedirectResponse
    {
        $titulo       = Titulo::findOrFail($request->id);
        $titulo->ativo = !$titulo->ativo;
        $titulo->save();

        $statusTexto = $titulo->ativo ? 'ativado' : 'desativado';
        return redirect()->route('gerenciar_titulos')->with('success', "Título {$statusTexto} com sucesso!");
    }

    // ─────────────────────────────────────────────────────────────
    //  Gerenciar Locais
    // ─────────────────────────────────────────────────────────────

    public function gerenciarLocais(): View
    {
        $locais = Local::orderBy('nome', 'asc')->get();
        return view('admin_gerenciar_locais', compact('locais'));
    }

    /**
     * Padrão Form Request: validação e autorização em SalvarLocalRequest.
     */
    public function salvarLocal(SalvarLocalRequest $request): RedirectResponse
    {
        Local::create($request->only(['nome', 'endereco', 'tipo']));
        return redirect()->route('gerenciar_locais')->with('success', 'Local criado com sucesso!');
    }

    public function atualizarLocal(SalvarLocalRequest $request): RedirectResponse
    {
        $local = Local::findOrFail($request->id);
        $local->update($request->only(['nome', 'endereco', 'tipo']));
        return redirect()->route('gerenciar_locais')->with('success', 'Local atualizado com sucesso!');
    }

    public function alternarStatusLocal(Request $request): RedirectResponse
    {
        $local       = Local::findOrFail($request->id);
        $local->ativo = !$local->ativo;
        $local->save();

        $statusTexto = $local->ativo ? 'ativado' : 'desativado';
        return redirect()->route('gerenciar_locais')->with('success', "Local {$statusTexto} com sucesso!");
    }
}