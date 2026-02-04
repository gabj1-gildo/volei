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
        $jogosAgrupados = Jogo::with(['titulo', 'local', 'user'])->get()->groupBy(fn($j) => $j->user->name);
        $titulos = Titulo::all();
        $locais = Local::all();
        
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

        // // Se for admin, validamos também o campo do responsável
        // if (auth()->user()->tipo === 'admin') {
        //     $rules['responsavel_id'] = 'required|exists:users,id';
        // }

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

    public function excluirJogo(Request $request)
    {
        if (Auth::user()->tipo !== 'admin') abort(403);

        $jogo = Jogo::findOrFail($request->id);
        $jogo->delete();

        return redirect()->back()->with('success', 'Jogo removido.');
    }

    //-----------------------------------------------------------------------------------------//

    // Gerenciar Inscrições (admin_gerenciar_inscricoes.blade.php)
    public function gerenciarInscricoes(Request $request) {
        $query = Jogo::with(['inscricoes.user', 'titulo', 'local']);
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
            'username'=> 'required|exists:users,username',
            'tipo' => 'required|in:admin,organizador,jogador',
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Impede que o admin logado altere o próprio tipo (evita ficar sem admin no sistema)
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode alterar seu próprio nível de acesso.');
        }

        $user->update(['tipo' => $request->tipo]);

        return back()->with('success', "O nível de acesso de {$user->name} foi atualizado!");
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
        return redirect()->route('gerenciar_titulos');
    }

    public function atualizarTitulo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:titulos,id',
            'nome' => 'required|max:255'
        ]);

        $titulo = Titulo::findOrFail($request->id);
        $titulo->update($request->only('nome'));
        return redirect()->route('gerenciar_titulos');
    }

    public function excluirTitulo(Request $request)
    {
        $titulo = Titulo::findOrFail($request->id);
        $titulo->delete();
        return redirect()->route('gerenciar_titulos');
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
    return redirect()->route('gerenciar_locais');
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
    return redirect()->route('gerenciar_locais');
}

public function excluirLocal(Request $request)
{
    $local = Local::findOrFail($request->id);
    $local->delete();
    return redirect()->route('gerenciar_locais');
}

}