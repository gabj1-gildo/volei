<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Adicione a importação aqui no topo

class User extends Authenticatable implements MustVerifyEmail 
{
    // 2. Coloque o SoftDeletes aqui dentro junto com os outros
    use HasFactory, Notifiable, SoftDeletes; 
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'tipo',
        'username_updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'username_updated_at' => 'datetime',
        ];
    }

    // funções alem das padrões
    public function inscricoes() {
        return $this->hasMany(Inscricao::class);
    }
    
    // Aproveitei para deixar a relação de jogos pronta caso você precise listar os jogos desse usuário no futuro
    public function jogos() {
        return $this->hasMany(Jogo::class);
    }
}