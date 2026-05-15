<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expande o campo 'status' da tabela jogos de string simples para suportar
     * os 5 estados do State Pattern: aberto, inscricoes_encerradas,
     * em_andamento, cancelado, encerrado.
     *
     * SQLite (banco de desenvolvimento) não suporta ALTER COLUMN diretamente,
     * por isso usamos a estratégia de coluna temporária + renomeação.
     */
    public function up(): void
    {
        Schema::table('jogos', function (Blueprint $table) {
            // Cria coluna temporária
            $table->string('status_new')->default('aberto')->after('descricao');
        });

        // Copia os dados existentes
        DB::table('jogos')->update(['status_new' => DB::raw('status')]);

        Schema::table('jogos', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('jogos', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    /**
     * Volta a coluna status para string simples (rollback).
     */
    public function down(): void
    {
        // Em SQLite, um renomeamento é suficiente para reverter
        Schema::table('jogos', function (Blueprint $table) {
            $table->string('status_rollback')->default('aberto')->after('descricao');
        });

        DB::table('jogos')->update(['status_rollback' => DB::raw('status')]);

        Schema::table('jogos', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('jogos', function (Blueprint $table) {
            $table->renameColumn('status_rollback', 'status');
        });
    }
};
