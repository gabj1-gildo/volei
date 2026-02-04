<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('jogos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Organizador logado
            $table->foreignId('titulo_id'); 
            $table->foreignId('local_id');
            $table->dateTime('data_hora');
            $table->dateTime('data_hora_limite_inscricao');
            $table->integer('limite_jogadores');
            $table->text('descricao')->nullable();
            $table->enum('status', ['aberto', 'encerrado', 'cancelado'])->default('aberto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jogos');
    }
};
