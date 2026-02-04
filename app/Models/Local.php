<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    //nome da tabela
    protected $table = 'locais';

    //campos preenchiveis
    protected $fillable = [
        'nome',
        'endereco',
        'tipo',
    ];
}
