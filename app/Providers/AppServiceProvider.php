<?php

namespace App\Providers;

use App\Models\Inscricao;
use App\Observers\InscricaoObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Padrão Observer: registra InscricaoObserver para que vagas_disponiveis
     * seja recalculado automaticamente após qualquer alteração em Inscricao.
     */
    public function boot(): void
    {
        // Observer: reage aos eventos saved/deleted do Model Inscricao
        Inscricao::observe(InscricaoObserver::class);

        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
