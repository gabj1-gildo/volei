<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\JogadorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerifyEmailController;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rota inicial - Pública
Route::get('/', function () {
    return view('welcome');
});


Route::get('/disparar-email', function () {
    // 1. Pega o usuário logado
    $usuario = Auth::user();

    // 2. Validação simples caso não tenha ninguém logado
    if (!$usuario) {
        return "Erro: Você precisa estar logado para o sistema saber para quem enviar.";
    }

    // 3. O "Pulo do Gato": Enviando sem precisar de View ou Mailable
    Mail::html("<p>Olá <strong>{$usuario->name}</strong>,</p><p>Este e-mail foi disparado via rota de teste!</p>", function ($message) use ($usuario) {
        $message->to($usuario->email)
                ->subject("Teste de Sistema - " . $usuario->name);
    });

    return "Sucesso! E-mail enviado para: " . $usuario->email;
});
// --- GRUPO DE USUÁRIOS AUTENTICADOS ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // DASHBOARD (Página principal com a vitrine de jogos)
    Route::get('/dashboard', [JogadorController::class, 'dashboard'])->name('dashboard');

    // PERFIL (Breeze padrão)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // FUNÇÕES DO JOGADOR
    Route::get('/vitrine-jogos', [JogadorController::class, 'index'])->name('jogos_disponiveis');
    Route::post('/inscrever', [JogadorController::class, 'inscrever'])->name('fazer_inscricao');

    // --- GRUPO: ADMIN E ORGANIZADORES (Gestão da Logística) ---
    // Substituímos a função anônima pelo apelido (alias) do middleware que você criou manualmente
    Route::middleware(['is_organizador'])->group(function () {
        
        // Gestão de Jogos
        Route::get('/jogos', [AdminController::class, 'gerenciarJogos'])->name('gerenciar_jogos');
        Route::post('/jogos/salvar', [AdminController::class, 'salvarJogo'])->name('salvar_jogo');
        Route::get('/jogos/editar/{id}', [AdminController::class, 'editarJogo'])->name('editar_jogo');
        Route::post('/jogos/atualizar', [AdminController::class, 'atualizarJogo'])->name('atualizar_jogo');
        Route::get('/jogos/cancelar', [AdminController::class, 'cancelarJogo'])->name('cancelar_jogo');

        // Gestão de Inscrições
        Route::get('/gerenciar-inscricoes', [AdminController::class, 'gerenciarInscricoes'])->name('gerenciar_inscricoes');
        Route::post('/alterar-status-inscricao', [AdminController::class, 'alterarStatusInscricao'])->name('alterar_status_inscricao');
    });

    // --- GRUPO: EXCLUSIVO ADMIN (Gestão de Pessoas) ---
    Route::middleware(['is_admin'])->group(function () {
        Route::get('/usuarios', [AdminController::class, 'gerenciarUsuarios'])->name('gerenciar_usuarios');
        Route::post('/usuarios/atualizar-tipo', [AdminController::class, 'atualizarTipoUsuario'])->name('atualizar_tipo_usuario');

        //Gerenciamento de titulos
        Route::get('/titulos', [AdminController::class, 'gerenciarTitulos'])->name('gerenciar_titulos');
        Route::post('/titulos/salvar', [AdminController::class, 'salvarTitulo'])->name('salvar_titulo');
        Route::post('/titulos/atualizar', [AdminController::class, 'atualizarTitulo'])->name('atualizar_titulo');
        Route::delete('/titulos/excluir', [AdminController::class, 'excluirTitulo'])->name('excluir_titulo');

        //Gerenciamento de locais
        Route::get('/locais', [AdminController::class, 'gerenciarLocais'])->name('gerenciar_locais');
        Route::post('/locais/salvar', [AdminController::class, 'salvarLocal'])->name('salvar_local');
        Route::post('/locais/atualizar', [AdminController::class, 'atualizarLocal'])->name('atualizar_local');
        Route::delete('/locais/excluir', [AdminController::class, 'excluirLocal'])->name('excluir_local');
    });

});

require __DIR__.'/auth.php';
