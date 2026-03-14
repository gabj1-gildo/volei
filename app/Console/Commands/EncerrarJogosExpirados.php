<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jogo; // Importe seu Model Jogo
use Illuminate\Support\Carbon;

class EncerrarJogosExpirados extends Command
{
    // Esse é o nome que você colocou no Schedule do Docker
    protected $signature = 'jogos:encerrar';

    protected $description = 'Verifica a data limite e encerra os jogos no banco de dados';

    public function handle()
    {
        // 1. Pegamos a hora atual (ajustada pelo timezone do Laravel)
        $agora = Carbon::now();

        // 2. Executamos a atualização em massa no banco
        $quantidade = Jogo::where('status', '!=', 'encerrado') // Só quem não está encerrado
            ->where('data_hora', '<=', $agora)           // Onde a data já passou
            ->update(['status' => 'encerrado']);           // Altera a coluna status

        // 3. Log para você visualizar no console do Render
        // if ($quantidade > 0) {
        //     $this->info("Sucesso: {$quantidade} jogos foram atualizados para 'encerrado'.");
        // }
    }
}