<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PiraVoleiSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. ADMIN (Acesso total e gestão de usuários)
        User::create([
            'name' => 'Administrador PiraVôlei',
            'username' => 'admin_pira',
            'email' => 'admin@piravolei.com',
            'password' => Hash::make('senha123'),
            'tipo' => 'admin',
            'username_updated_at' => now()->subDays(10), // Já pode trocar o username
        ]);

        // 2. ORGANIZADOR (Pode criar jogos e locais, mas não gere usuários)
        User::create([
            'name' => 'Organizador de Partidas',
            'username' => 'organiza_volei',
            'email' => 'organizador@piravolei.com',
            'password' => Hash::make('senha123'),
            'tipo' => 'organizador',
            'username_updated_at' => now()->subDays(2), // Só poderá trocar em 5 dias
        ]);

        // 3. JOGADOR (Apenas vitrine e inscrições)
        User::create([
            'name' => 'Atleta Pirapora',
            'username' => 'jogador_top',
            'email' => 'jogador@piravolei.com',
            'password' => Hash::make('senha123'),
            'tipo' => 'jogador',
            'username_updated_at' => now(), // Troca apenas em 7 dias
        ]);

        $this->command->info('Usuários do PiraVôlei criados com sucesso!');
    }
}