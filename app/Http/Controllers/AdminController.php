<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jogo;
use App\Models\Local;
use App\Models\Titulo;
use App\Models\Inscricao;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AdminController extends Controller
{
    // Listagem de Jogos (admin_gerenciar_jogos.blade.php)
    public function gerenciarJogos()
    {
        $jogosAgrupados = Jogo::with(['titulo', 'local', 'responsavel'])
            ->withCount(['inscricoes' => function ($query) {
                $query->whereNotIn('status', ['cancelada']);
            }])
            ->get()
            ->groupBy(fn($j) => $j->responsavel->name);

        $titulos = Titulo::where('ativo', true)->get();
        $locais = Local::where('ativo', true)->get();
        
        // Busca apenas usuários que são organizadores para o Admin poder escolher
        $organizadores = User::where('tipo', 'organizador')->get();

        return view('admin_gerenciar_jogos', compact('jogosAgrupados', 'titulos', 'locais', 'organizadores'));
    }

    public function salvarJogo(Request $request)
    {
        $request->validate([
            'titulo' => 'required|exists:titulos,id',
            'local' => 'required|exists:locais,id',
            // Impede datas retroativas no servidor
            'data' => 'required|date|after_or_equal:today', 
            'hora' => 'required',
            'data_limite_inscricao' => 'required|date|after_or_equal:today|before_or_equal:data',
            'hora_limite_inscricao' => 'required',
            'limite_jogadores' => 'required|integer|min:1',
            'descricao' => 'nullable|string|max:1000',
        ], [
            'data.after_or_equal' => 'O jogo não pode ser marcado para uma data passada.',
            'data_limite_inscricao.before_or_equal' => 'A inscrição deve encerrar antes ou no dia do jogo.',
        ]);

        // 2. Processamento com Carbon
        $dataHoraJogo = Carbon::parse($request->data . ' ' . $request->hora);
        $dataHoraLimite = Carbon::parse($request->data_limite_inscricao . ' ' . $request->hora_limite_inscricao);
        $agora= Carbon::now();

        // 3. Validações de Regra de Negócio
        if ($dataHoraJogo->isPast()) {
            return back()->withErrors(['data' => 'O jogo não pode ser marcado para uma data/hora passada.'])->withInput();
        }

        if ($dataHoraLimite->gt($dataHoraJogo)) {
            return back()->withErrors(['data_limite_inscricao' => 'As inscrições devem encerrar antes do início do jogo.'])->withInput();
        }

        if ($dataHoraLimite->isPast()) {
            return back()->withErrors(['data_limite_inscricao' => 'A data limite de inscrição já passou.'])->withInput();
        }

        // 4. Definição do Responsável e Criação
        $responsavelId = (auth()->user()->tipo === 'admin' && $request->filled('responsavel_id')) 
                        ? $request->responsavel_id 
                        : auth()->id();

        Jogo::create([
            'user_id' => $responsavelId,
            'titulo_id' => $request->titulo,
            'local_id' => $request->local,
            'data_hora' => $dataHoraJogo,
            'data_hora_limite_inscricao' => $dataHoraLimite,
            'limite_jogadores' => $request->limite_jogadores,
            'descricao' => $request->descricao,
            'status' => 'aberto'
        ]);

        return redirect()->route('gerenciar_jogos')->with('success', 'Jogo criado com sucesso!');
    }

    public function atualizarJogo(Request $request)
    {
        $request->validate([
            'id'                    => 'required|exists:jogos,id',
            'titulo'                => 'required|exists:titulos,id',
            'local'                 => 'required|exists:locais,id',
            'data'                  => 'required|date',
            'hora'                  => 'required',
            'limite_jogadores'      => 'required|integer|min:1',
            'data_limite_inscricao' => 'required|date',
            'hora_limite_inscricao' => 'required',
            'descricao'             => 'nullable|string|max:1000',
        ]);

        $partida = Jogo::findOrFail($request->id);
        
        $dataHoraJogo = Carbon::parse($request->data . ' ' . $request->hora);
        $dataHoraLimite = Carbon::parse($request->data_limite_inscricao . ' ' . $request->hora_limite_inscricao);

        // Validação: Jogo deve ser após a inscrição
        if ($dataHoraLimite->gt($dataHoraJogo)) {
            return back()->withErrors(['data_limite_inscricao' => 'A data limite de inscrição não pode ser após o início do jogo.']);
        }

        // Validação: Se o jogo for alterado para o passado
        if ($dataHoraJogo->isPast() && $partida->data_hora != $dataHoraJogo) {
            return back()->withErrors(['data' => 'Você não pode alterar um jogo para uma data que já passou.']);
        }

        $partida->update([
            'titulo_id'                  => $request->titulo,
            'local_id'                   => $request->local,
            'data_hora'                  => $dataHoraJogo,
            'limite_jogadores'           => $request->limite_jogadores,
            'data_hora_limite_inscricao' => $dataHoraLimite,
            'descricao'                  => $request->descricao,
            'user_id'                    => $request->filled('responsavel_id') ? $request->responsavel_id : $partida->user_id,
        ]);

        return back()->with('success', 'Jogo atualizado com sucesso!');
    }

    public function alterarStatusJogo(Request $request) 
    {
        $request->validate([
            'id_partida' => 'required|exists:jogos,id',
            'status'     => 'required|string|in:aberto,inscricoes_encerradas,em_andamento,cancelado,encerrado' 
        ]);

        $jogo = Jogo::findOrFail($request->id_partida);
        $jogo->update(['status' => $request->status]);

        return back()->with('success', 'Status da partida atualizado!');
    }

    //-----------------------------------------------------------------------------------------//

    // Gerenciar Inscrições (admin_gerenciar_inscricoes.blade.php)
    public function gerenciarInscricoes(Request $request) {
        $query = Jogo::with(['inscricoes.user', 'titulo', 'local'])
                    ->where('status', 'aberto');
        if($request->jogo) $query->where('id', $request->jogo);
        $jogos = $query->get();
        return view('admin_gerenciar_inscricoes', compact('jogos'));
    }

    // Alterar status (Aprovar/Recusar em admin_gerenciar_inscricoes)
    public function alterarStatusInscricao(Request $request) {
        $inscricao = Inscricao::findOrFail($request->id_inscricao);
        $inscricao->update(['status' => $request->status]);
        return back()->with('success', 'Status atualizado!');
    }

    //-----------------------------------------------------------------------------------------//

    //Métodos para Gerenciar Usuários
    public function gerenciarUsuarios()
    {
        // Busca todos os usuários ordenados por nome
        $usuarios = User::orderBy('name', 'asc')->get();
        return view('admin_gerenciar_usuarios', compact('usuarios'));
    }

    public function atualizarTipoUsuario(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipo' => 'required|in:admin,organizador,jogador',
        ]);

        $userAlvo = User::findOrFail($request->user_id);
        $adminLogado = auth()->user();

        // REGRA 1: Impede que o admin altere a si mesmo
        if ($userAlvo->id === $adminLogado->id) {
            return back()->with('error', 'Você não pode alterar seu próprio nível de acesso.');
        }

        // REGRA 2: Impede que um admin altere outro admin
        if ($userAlvo->tipo === 'admin') {
            return back()->with('error', 'Segurança: Um administrador não pode alterar o cargo de outro administrador.');
        }

        $userAlvo->update(['tipo' => $request->tipo]);

        return back()->with('success', "O nível de acesso de {$userAlvo->name} foi atualizado!");
    }

    //-----------------------------------------------------------------------------------------//

    // Métodos para Gerenciar Títulos
    public function gerenciarTitulos()
    {
        $titulos = Titulo::orderBy('nome', 'asc')->get();
        return view('admin_gerenciar_titulos', compact('titulos'));
    }

    public function salvarTitulo(Request $request)
    {
        $request->validate([
            'nome' => 'required|max:255'
        ]);

        Titulo::create($request->only('nome'));
        return redirect()->route('gerenciar_titulos')->with('success', "Título criado com sucesso!");
    }

    public function atualizarTitulo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:titulos,id',
            'nome' => 'required|max:255'
        ]);

        $titulo = Titulo::findOrFail($request->id);
        $titulo->update($request->only('nome'));
        return redirect()->route('gerenciar_titulos')->with('success', "Título atualizado com sucesso!");
    }

    public function alternarStatusTitulo(Request $request)
    {
        $titulo = Titulo::findOrFail($request->id);
        
        // Inverte o status atual
        $titulo->ativo = !$titulo->ativo;
        $titulo->save();

        $statusTexto = $titulo->ativo ? 'ativado' : 'desativado';
        return redirect()->route('gerenciar_titulos')->with('success', "Título $statusTexto com sucesso!");
    }

    //-----------------------------------------------------------------------------------------//
    
    // Métodos para Gerenciar Locais
    public function gerenciarLocais()
{
    $locais = Local::orderBy('nome', 'asc')->get();
    return view('admin_gerenciar_locais', compact('locais'));
}

public function salvarLocal(Request $request)
{
    $request->validate([
        'nome' => 'required|max:255',
        'endereco' => 'required|max:255',
        'tipo' => 'required|in:publico,privado',
    ]);

    Local::create($request->all());
    return redirect()->route('gerenciar_locais')->with('success', "Local criado com sucesso!");
}

public function atualizarLocal(Request $request)
{
    $request->validate([
        'id' => 'required|exists:locais,id',
        'nome' => 'required|max:255',
        'endereco' => 'required|max:255',
        'tipo' => 'required|in:publico,privado',
    ]);

    $local = Local::findOrFail($request->id);
    $local->update($request->only(['nome', 'endereco', 'tipo']));
    return redirect()->route('gerenciar_locais')->with('success', "Local atualizado com sucesso!");
}

public function alternarStatusLocal(Request $request)
    {
        $local = Local::findOrFail($request->id);
        
        // Inverte o status atual
        $local->ativo = !$local->ativo;
        $local->save();

        $statusTexto = $local->ativo ? 'ativado' : 'desativado';
        return redirect()->route('gerenciar_locais')->with('success', "Local $statusTexto com sucesso!");
    }

}