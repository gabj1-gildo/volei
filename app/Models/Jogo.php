<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jogo extends Model {
    protected $fillable = ['user_id', 'titulo_id', 'local_id', 'data_hora', 'data_hora_limite_inscricao', 'limite_jogadores', 'descricao', 'status'];

    public function responsavel() { return $this->belongsTo(User::class, 'user_id'); }
    public function local() { return $this->belongsTo(Local::class); }
    public function titulo() { return $this->belongsTo(Titulo::class); }
    public function inscricoes() { return $this->hasMany(Inscricao::class); }
}

