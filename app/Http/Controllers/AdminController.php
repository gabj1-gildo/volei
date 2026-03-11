<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jogo;
use App\Models\Local;
use App\Models\Titulo;
use App\Models\Inscricao;
use Illuminate\Http\Request;
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
        $titulos= Titulo::where('ativo', true)->get();
        $locais= Local::where('ativo', true)->get();
        
        // Busca apenas usuários que são organizadores para o Admin poder escolher
        $organizadores = User::where('tipo', 'organizador')->get();

        return view('admin_gerenciar_jogos', compact('jogosAgrupados', 'titulos', 'locais', 'organizadores'));
    }

    public function salvarJogo(Request $request)
    {
        // 1. Validação
        $rules = [
            'titulo' => 'required|exists:titulos,id',
            'local' => 'required|exists:locais,id',
            'data' => 'required|date',
            'hora' => 'required',
            'data_limite_inscricao' => 'required|date',
            'hora_limite_inscricao' => 'required',
            'limite_jogadores' => 'required|integer|min:1',
        ];

        $request->validate($rules);

        // 2. Processamento das Datas
        $dataHoraJogo = $request->data . ' ' . $request->hora;
        $dataHoraLimite = $request->data_limite_inscricao . ' ' . $request->hora_limite_inscricao;

        // 3. Definição do Responsável (A regra solicitada)
        $responsavelId = (auth()->user()->tipo === 'admin') 
                        ? $request->responsavel_id 
                        : auth()->id();

        // 4. Criação
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

    //editar jogo
    public function editarJogo(Request $request)
    {
        // 1. Validação dos dados recebidos do formulário
        $request->validate([
            'id'                    => 'required|exists:partidas,id', // troque 'partidas' pelo nome correto da sua tabela se for diferente
            'titulo'                => 'required|integer',
            'local'                 => 'required|integer',
            'data'                  => 'required|date',
            'hora'                  => 'required',
            'limite_jogadores'      => 'required|integer|min:1',
            'data_limite_inscricao' => 'required|date',
            'hora_limite_inscricao' => 'required',
            'descricao'             => 'nullable|string|max:1000',
        ]);

        // 2. Busca a partida no banco de dados
        $partida = Partida::findOrFail($request->id);

        // 3. Junta as datas e horas para o formato do banco (Y-m-d H:i:s)
        $dataHora = $request->data . ' ' . $request->hora . ':00';
        $dataLimite = $request->data_limite_inscricao . ' ' . $request->hora_limite_inscricao . ':00';

        // 4. Atualiza os dados no banco
        $partida->update([
            'titulo_id'             => $request->titulo,
            'local_id'              => $request->local,
            'data_hora'             => $dataHora,
            'limite_jogadores'      => $request->limite_jogadores,
            'data_limite_inscricao' => $dataLimite,
            'descricao'             => $request->descricao,
            // O responsavel_id só é atualizado se for enviado na request (ex: se for admin)
            'responsavel_id'        => $request->has('responsavel_id') ? $request->responsavel_id : $partida->responsavel_id,
        ]);

        // 5. Retorna para a view com a mensagem de sucesso para o Toast
        return back()->with('success', 'Jogo atualizado com sucesso!');
    }

    public function alterarStatusJogo(Request $request) {
    $request->validate([
        'status'     => 'required|string' // ou in:agendado,em_andamento,finalizado
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