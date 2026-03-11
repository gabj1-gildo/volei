@extends('layouts.main_layout')

@section('content')

    <style>
        /* Estilização Premium baseada na identidade visual PiraVôlei */
        .game-card {
            border-radius: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.04);
            background-color: #ffffff;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .game-card:hover {
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.08);
        }

        /* Adaptação para o Modo Escuro */
        [data-bs-theme="dark"] .game-card {
            background-color: #162231;
            border-color: rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
        }
        
        [data-bs-theme="dark"] .game-card-header,
        [data-bs-theme="dark"] .inscricao-item {
            background-color: transparent !important;
            border-color: rgba(255,255,255,0.05) !important;
        }

        .btn-action-pira {
            border-radius: 1rem;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
        }

        /* Estilo para os itens da lista que substituíram a tabela */
        .inscricao-item {
            transition: background-color 0.2s;
            border-left: none;
            border-right: none;
            border-top: none;
        }
        
        .inscricao-item:last-child {
            border-bottom: none;
        }

        .inscricao-item:hover {
            background-color: rgba(0,0,0,0.02);
        }
        
        [data-bs-theme="dark"] .inscricao-item:hover {
            background-color: rgba(255,255,255,0.02);
        }
    </style>

    {{-- Sistema de Alertas (Toast) Padronizado --}}
    @if(session('success') || session('error'))
        @php
            $isSuccess = session('success') ? true : false;
            $mensagem = session('success') ?? session('error');
            $corFundo = $isSuccess ? 'bg-success' : 'bg-danger';
            $icone = $isSuccess ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        @endphp

        <div class="toast-container position-fixed top-0 end-0 p-3 mt-5" style="z-index: 1080">
            <div id="toastAlerta" class="toast align-items-center text-white {{ $corFundo }} border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body fw-bold">
                        <i class="bi {{ $icone }} me-2"></i>
                        <span>{{ $mensagem }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="container mt-5">
        
        {{-- Cabeçalho Principal da Página --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-5 border-bottom pb-3 gap-3">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-people-fill me-2 text-primary"></i>Gerenciamento de Inscrições
            </h4>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 shadow-sm px-4 py-2 rounded-pill fw-bold" style="font-size: 0.9rem;">
                <i class="bi bi-shield-lock-fill me-1"></i> Painel do Organizador
            </span>
        </div>

        {{-- Verifica se existem jogos para exibir --}}
        @if($jogos->isEmpty())
            <div class="card shadow-sm border-0 p-5 text-center game-card">
                <div class="fs-1 mb-3">📋</div>
                <h4 class="text-secondary fw-bold mb-0">Nenhum jogo ativo encontrado no momento.</h4>
                <p class="text-muted mt-2">Crie novas partidas para começar a receber inscrições de jogadores.</p>
            </div>
        @else
            
            {{-- Laço que cria um Cartão completo para CADA Jogo --}}
            @foreach($jogos as $jogo)
                <div class="card game-card mb-5 border-0">
                    <div class="card-body p-0">
                        
                        {{-- Cabeçalho do Cartão (Dados do Jogo) --}}
                        <div class="game-card-header bg-light p-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3 border border-warning border-opacity-25 shadow-sm" style="width: 50px; height: 50px; flex-shrink: 0;">
                                    <i class="bi bi-controller fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                                        {{ $jogo->titulo->nome ?? 'Sem Título' }}
                                    </h5>
                                    <div class="small text-muted mt-1 fw-medium d-flex flex-wrap gap-3">
                                        <span><i class="bi bi-calendar-event me-1"></i> {{ date('d/m/Y \à\s H:i', strtotime($jogo->data_hora)) }}</span>
                                        <span><i class="bi bi-geo-alt me-1"></i> {{ $jogo->local->nome ?? 'Local Indefinido' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2">
                                    <i class="bi bi-person-badge me-1"></i> Org: {{ explode(' ', $jogo->responsavel->name ?? 'Indefinido')[0] }}
                                </span>
                            </div>
                        </div>

                        {{-- Lista de Inscrições Responsiva (Substitui a Tabela) --}}
                        <div class="list-group list-group-flush">
                            
                            {{-- Cabeçalho da Lista (Oculto no Mobile, Visível no Desktop) --}}
                            @if($jogo->inscricoes->isNotEmpty())
                                <div class="list-group-item bg-transparent text-muted small fw-bold py-2 d-none d-md-flex px-4">
                                    <div style="flex: 2;">JOGADOR</div>
                                    <div style="flex: 1;" class="text-center">STATUS</div>
                                    <div style="flex: 1;" class="text-center">AÇÕES</div>
                                </div>
                            @endif

                            @forelse($jogo->inscricoes as $inscricao)
                                <div class="list-group-item inscricao-item p-4 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3">
                                    
                                    {{-- Jogador --}}
                                    <div class="d-flex align-items-center w-100" style="flex: 2;">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3 border border-primary border-opacity-25" style="width: 45px; height: 45px; flex-shrink: 0;">
                                            <i class="bi bi-person-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <span class="d-block small text-muted fw-bold d-md-none mb-1">JOGADOR</span>
                                            <span class="fw-bold fs-6">{{ $inscricao->user->name }}</span>
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="w-100 text-md-center mt-2 mt-md-0" style="flex: 1;">
                                        @php
                                            $badgeClass = match($inscricao->status) {
                                                'confirmado' => 'bg-success text-white',
                                                'pendente'   => 'bg-warning text-dark',
                                                'cancelada'  => 'bg-danger text-white',
                                                default      => 'bg-secondary text-white'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.75rem;">
                                            {{ strtoupper($inscricao->status) }}
                                        </span>
                                    </div>

                                    {{-- Ações --}}
                                    <div class="w-100 mt-3 mt-md-0 d-flex justify-content-md-center" style="flex: 1;">
                                        <form action="{{ route('alterar_status_inscricao') }}" method="POST" class="m-0 d-flex gap-2 w-100 justify-content-start justify-content-md-center">
                                            @csrf
                                            <input type="hidden" name="id_inscricao" value="{{ $inscricao->id }}">
                                            
                                            <button type="submit" name="status" value="confirmado" class="btn btn-outline-success btn-action-pira flex-fill flex-md-grow-0" {{ $inscricao->status === 'confirmado' ? 'disabled' : '' }}>
                                                <i class="bi bi-check-lg me-1"></i> Aprovar
                                            </button>

                                            <button type="submit" name="status" value="cancelada" class="btn btn-outline-danger btn-action-pira flex-fill flex-md-grow-0" {{ $inscricao->status === 'cancelada' ? 'disabled' : '' }}>
                                                <i class="bi bi-x-lg me-1"></i> Recusar
                                            </button>
                                        </form>
                                    </div>
                                    
                                </div>
                            @empty
                                <div class="text-center py-5 text-secondary">
                                    <i class="bi bi-inbox fs-2 d-block mb-3 text-muted opacity-50"></i>
                                    <span class="fw-medium">Nenhuma inscrição registrada para este jogo até o momento.</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success') || session('error'))
                const toastEl = document.getElementById('toastAlerta');
                if (toastEl) {
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                }
            @endif
        });
    </script>
@endsection 