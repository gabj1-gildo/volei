<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscricao extends Model
{
    protected $table = 'inscricoes';
    protected $fillable = ['jogo_id', 'user_id', 'status'];

    /**
     * Relacionamento: Uma inscrição pertence a um Jogo
     */
    public function jogo(): BelongsTo
    {
        return $this->belongsTo(Jogo::class, 'jogo_id');
    }

    /**
     * Relacionamento: Uma inscrição pertence a um Usuário (Jogador)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}