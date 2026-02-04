@extends('layouts.main_layout')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col">
            
            {{-- Área de botões de gestão: visível apenas para Admin ou Organizador
            @if(Auth::user()->tipo == 'admin' || Auth::user()->tipo == 'organizador')
                <div class="row mt-4">
                    <div class="col text-center">
                        <div class="btn-group shadow-sm">
                            <a href="{{ route('gerenciar_inscricoes') }}" class="btn btn-info text-white">
                                <i class="bi bi-people"></i> Gerenciar Inscrições
                            </a>
                            <a href="{{ route('gerenciar_jogos') }}" class="btn btn-success">
                                <i class="bi bi-controller"></i> Gerenciar Jogos
                            </a>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
            @endif --}}

            {{-- Listagem de Jogos Disponíveis --}}
            @if($jogos->isEmpty())
                <div class="row mt-5">
                    <div class="col text-center">
                        <h3 class="text-secondary">Não há jogos disponíveis no momento.</h3>
                    </div>
                </div>
            @else
                @if(session('success'))
                    <div class="alert alert-success text-center shadow-sm mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="row">
                    @foreach($jogos as $jogo)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body d-flex flex-column">
                                    <h3 class="card-title text-center fw-bold text-primary">
                                        {{ $jogo->titulo->nome ?? 'Título' }}
                                    </h3>
                                    <hr>
                                    
                                    <p class="card-text mb-2">
                                        <strong><i class="bi bi-calendar-event"></i> Data:</strong> 
                                        {{ date('d/m/Y H:i', strtotime($jogo->data_hora)) }}
                                    </p>
                                    
                                    <p class="card-text mb-2">
                                        <strong><i class="bi bi-clock-history"></i> Inscrições até:</strong> 
                                        {{ date('d/m/Y H:i', strtotime($jogo->data_hora_limite_inscricao)) }}
                                    </p>   
                                    
                                    <p class="card-text mb-2 text-muted flex-grow-1">
                                        <strong>Descrição:</strong> {{ Str::limit($jogo->descricao, 80) }}
                                    </p>
                                    
                                    <p class="card-text mb-2">
                                        <strong><i class="bi bi-geo-alt"></i> Local:</strong> 
                                        {{ $jogo->local->nome ?? 'A definir' }}
                                    </p>
                                
                                    <p class="card-text mb-2">
                                        <strong><i class="bi bi-person-badge"></i> Organizador:</strong> 
                                        {{ $jogo->responsavel->name ?? 'Sistema' }}
                                    </p>
                                    
                                    <p class="card-text mb-3">
                                        <strong>Vagas Disponíveis:</strong> 
                                        <span class="badge {{ ($jogo->vagas - $jogo->inscricoes_count) > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $jogo->vagas - $jogo->inscricoes_count }} de {{ $jogo->vagas }}
                                        </span>
                                    </p>

                                    <div class="mt-auto pt-3 border-top">
                                        <form action="{{ route('fazer_inscricao') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="jogo_id" value="{{ Crypt::encrypt($jogo->id) }}">
                                            <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm">
                                                Quero Participar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection