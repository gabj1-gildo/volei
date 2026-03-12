@extends('layouts.main_layout')

@section('content')
    <style>
        /* Estilização Geral e Header */
        .page-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            border-radius: 1.5rem;
            padding: 2.5rem;
            margin-bottom: 3rem;
            color: white;
            box-shadow: 0 10px 25px rgba(30, 58, 138, 0.2);
        }

        .game-history-card {
            border-radius: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            background: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .game-history-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        /* Faixa de Status (Ribbon) - CORES ATUALIZADAS */
        .status-badge {
            position: absolute;
            top: 1.25rem;
            right: -35px;
            transform: rotate(45deg);
            width: 150px;
            text-align: center;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0.4rem 0;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        /* Classes de Cores dos Status */
        .status-confirmado { background-color: #10b981; color: white; } /* Verde */
        .status-pendente { background-color: #f59e0b; color: white; } /* Amarelo/Laranja */
        .status-cancelada, .status-recusada { background-color: #ef4444; color: white; } /* Vermelho */

        /* Estilo para Jogos que não estão mais abertos */
        .card-past {
            opacity: 0.85;
            background-color: #f9fafb;
        }
        
        [data-bs-theme="dark"] .card-past { background-color: #111827; }

        .info-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.5px;
        }

        .dev-footer {
            font-size: 0.8rem;
            color: #9ca3af;
            text-align: center;
            margin-top: 5rem;
            padding-bottom: 2rem;
        }

        [data-bs-theme="dark"] .game-history-card {
            background: #1e293b;
            border-color: rgba(255,255,255,0.05);
        }
    </style>

    <div class="container mt-5" style="padding-bottom: 1.5rem">
        {{-- Header da Página --}}
        <div class="page-header d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-journal-check me-2"></i>Minhas Inscrições</h2>
                <p class="mb-0 opacity-75">Vizualize seu histórico de partidas e status de aprovação.</p>
            </div>
            <div class="fs-1 d-none d-md-block opacity-25">
                <i class="bi bi-volleyball"></i>
            </div>
        </div>

        <div class="row g-4">
            @forelse($inscricoes as $inscricao)
                @php 
                    $jogo = $inscricao->jogo;
                    $isPassado = in_array($jogo->status, ['cancelado', 'encerrado']);
                @endphp

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 game-history-card {{ $isPassado ? 'card-past' : '' }}">
                        
                        {{-- Faixa de Status da Inscrição --}}
                        <div class="status-badge status-{{ $inscricao->status }}">
                            @if ($inscricao->status === 'cancelada')
                                RECUSADA
                            @else
                                {{ strtoupper($inscricao->status) }}
                            @endif
                        </div>

                        <div class="card-body p-4">
                            {{-- Nome do Jogo --}}
                            <div class="mb-4">
                                <h5 class="fw-bold text-primary mb-1">{{ $jogo->titulo->nome ?? 'Partida sem título' }}</h5>
                                <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $jogo->local->nome ?? 'Local não informado' }}</span>
                            </div>

                            {{-- Detalhes em Grid --}}
                            <div class="row mb-4">
                                <div class="col-6 border-end">
                                    <div class="info-label">Data do Jogo</div>
                                    <div class="fw-bold small mt-1">
                                        <i class="bi bi-calendar-event me-1 text-primary"></i>
                                        {{ date('d/m/Y', strtotime($jogo->data_hora)) }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;">às {{ date('H:i', strtotime($jogo->data_hora)) }}</div>
                                </div>
                                <div class="col-6 ps-3">
                                    <div class="info-label">Sua Inscrição</div>
                                    <div class="mt-1 small">
                                        @if($inscricao->status === 'aprovada' || $inscricao->status === 'confirmada')
                                            <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Aprovado</span>
                                        @elseif($inscricao->status === 'pendente')
                                            <span class="text-warning fw-bold"><i class="bi bi-hourglass-split me-1"></i> Pendente</span>
                                        @else
                                            <span class="text-danger fw-bold"><i class="bi bi-x-octagon-fill me-1"></i> Recusado</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Status da Partida --}}
                            <div class="p-3 rounded-3 bg-opacity-10 {{ $jogo->status === 'aberto' ? 'bg-success text-success' : 'bg-secondary text-secondary' }} border mb-4 d-flex justify-content-between align-items-center">
                                <div class="small">
                                    <span class="info-label d-block" style="color: inherit; opacity: 0.7;">Status da Partida</span>
                                    <strong class="text-uppercase">{{ str_replace('_', ' ', $jogo->status) }}</strong>
                                </div>
                                @if($jogo->status === 'aberto' && in_array($inscricao->status, ['pendente', 'aprovada', 'confirmada']))
                                    <form action="{{ route('cancelar_inscricao') }}" method="POST" class="d-inline">
                                        @csrf
                                        {{-- Enviamos o ID da INSCRIÇÃO criptografado --}}
                                        <input type="hidden" name="inscricao_id" value="{{ encrypt($inscricao->id) }}">
                                        
                                        <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm fw-bold" 
                                                onclick="return confirm('Tem certeza que deseja cancelar sua participação nesta partida?')">
                                            <i class="bi bi-x-circle me-1"></i> DESINSCREVER
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- Rodapé Interno --}}
                            <div class="d-flex justify-content-between align-items-center opacity-50" style="font-size: 0.7rem;">
                                <span>Solicitado em: {{ $inscricao->created_at->format('d/m/Y H:i') }}</span>
                                <i class="bi bi-shield-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="display-1 text-muted opacity-25 mb-3">🏐</div>
                    <h4 class="text-secondary fw-bold">Você ainda não tem inscrições.</h4>
                    <p class="text-muted">Que tal procurar uma partida para jogar hoje?</p>
                    <a href="{{ route('home') }}" class="btn btn-lg btn-primary mt-3 rounded-pill px-5 shadow">Ver Jogos Disponíveis</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection