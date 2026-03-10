@extends('layouts.main_layout')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col">
                
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
                                            <span class="badge {{ ($jogo->limite_jogadores - $jogo->inscricoes_count) > 0 ? 'bg-info' : 'bg-danger' }}">
                                                {{ $jogo->limite_jogadores - $jogo->inscricoes_count }} de {{ $jogo->limite_jogadores }}
                                            </span>
                                        </p>

                                        {{-- botão para inscriver, cancelar inscrição --}}
                                        <div class="mt-auto pt-3 border-top">
                                            @php
                                                // Pega a inscrição do usuário logado para este jogo específico
                                                $minhaInscricao = $jogo->inscricoes->first(); 
                                            @endphp

                                            @if(!$minhaInscricao)
                                                {{-- Caso 1: Não está inscrito --}}
                                                <form action="{{ route('fazer_inscricao') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="jogo_id" value="{{ Crypt::encrypt($jogo->id) }}">
                                                    <button type="submit" class="btn btn-outline-success w-100 fw-bold shadow-sm">
                                                        Quero Participar
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Caso 2: Já possui inscrição (Verifica o status) --}}
                                                <div class="text-center">
                                                    @if($minhaInscricao->status == 'pendente')
                                                        <span class="badge bg-warning text-dark w-100 py-2 mb-2">
                                                            <i class="bi bi-clock"></i> Inscrição feita (Aguardando confirmação)
                                                        </span>
                                                    @elseif($minhaInscricao->status == 'confirmado')
                                                        <span class="badge bg-primary w-100 py-2 mb-2">
                                                            <i class="bi bi-check-circle"></i> Inscrição Confirmada!
                                                        </span>
                                                    @elseif($minhaInscricao->status == 'cancelada')
                                                        <span class="badge bg-danger w-100 py-2 mb-2">
                                                            Inscrição Cancelada
                                                        </span>
                                                        {{-- Se quiser permitir que ele se inscreva de novo após cancelar --}}
                                                        <form action="{{ route('fazer_inscricao') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="jogo_id" value="{{ Crypt::encrypt($jogo->id) }}">
                                                            <button type="submit" class="btn btn-outline-warning btn-sm w-100">Tentar novamente</button>
                                                        </form>
                                                    @endif

                                                    {{-- Botão de Cancelar (Visível se estiver pendente ou aprovado) --}}
                                                    @if($minhaInscricao->status != 'cancelada' && $minhaInscricao->status != 'recusada')
                                                        <form action="{{ route('cancelar_inscricao') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar sua vaga?')">
                                                            @csrf
                                                            <input type="hidden" name="inscricao_id" value="{{ Crypt::encrypt($minhaInscricao->id) }}">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                                Cancelar inscrição
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endif
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