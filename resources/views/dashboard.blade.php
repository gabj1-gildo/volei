@extends('layouts.main_layout')

@section('content')

    <style>
        /* Estilização Premium para os Cartões de Jogo */
        .game-card {
            border-radius: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.08) !important; /* Borda super suave */
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.04); /* Sombra longa e difusa */
            transition: all 0.3s ease;
            background-color: #ffffff;
            overflow: hidden;
        }

        .game-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.08); /* Sombra intensifica ao passar o mouse */
        }

        /* Adaptação perfeita para o Modo Escuro */
        [data-bs-theme="dark"] .game-card {
            background-color: #162231;
            border-color: rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
        }

        .btn-action-pira {
            border-radius: 1rem;
            font-weight: 700;
            padding: 0.7rem 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>

    {{-- Exibição de Erros e Sucesso (Toast) --}}
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
        <div class="row justify-content-center">
            <div class="col">

                {{-- Listagem de Jogos Disponíveis --}}
                @if($jogos->isEmpty())
                    <div class="card shadow-sm border-0 p-5 text-center" style="border-radius: 1.5rem;">
                        <div class="fs-1 mb-3">🏐</div>
                        <h4 class="text-secondary fw-bold mb-0">Não há jogos disponíveis no momento.</h4>
                        <p class="text-muted mt-2">Fique de olho, novas partidas podem surgir a qualquer hora!</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($jogos as $jogo)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 game-card">
                                    <div class="card-body p-4 d-flex flex-column">
                                        
                                        {{-- Cabeçalho do Cartão (Título) --}}
                                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom border-opacity-50">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                <i class="bi fs-2">🏐</i>
                                            </div>
                                            <h4 class="card-title mb-0 fw-bold" style="font-size: 1.2rem; line-height: 1.2;">
                                                {{ $jogo->titulo->nome ?? 'Título' }}
                                            </h4>
                                        </div>
                                        
                                        {{-- Informações do Jogo --}}
                                        <div class="d-flex flex-column gap-2 mb-4">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-calendar-event text-secondary mt-1 me-2"></i>
                                                <div>
                                                    <span class="d-block small text-muted fw-bold">DATA DA PARTIDA</span>
                                                    <span class="fw-medium">{{ date('d/m/Y \à\s H:i', strtotime($jogo->data_hora)) }}</span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-geo-alt text-secondary mt-1 me-2"></i>
                                                <div>
                                                    <span class="d-block small text-muted fw-bold">LOCAL</span>
                                                    <span class="fw-medium">{{ $jogo->local->nome ?? 'A definir' }}</span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-clock-history text-warning mt-1 me-2"></i>
                                                <div>
                                                    <span class="d-block small text-warning  fw-bold">INSCRIÇÕES ATÉ</span>
                                                    <span class="fw-medium">{{ date('d/m/Y H:i', strtotime($jogo->data_hora_limite_inscricao)) }}</span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-exclamation-circle text-info mt-1 me-2"></i>
                                                <div>
                                                    <span class="d-block small text-info fw-bold">DESCRIÇÃO</span>
                                                    <p class="card-text small text-secondary flex-grow-1 border-start border-2 border-primary ps-3 py-1 bg-light rounded-end">
                                                        {{ Str::limit($jogo->descricao, 90) }}
                                                    </p>
                                                </div>
                                            </div>

                                        </div>                                        
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div class="small">
                                                <span class="text-muted"><i class="bi bi-person-badge"></i> Org:</span> 
                                                <span class="fw-bold">{{ explode(' ', $jogo->responsavel->name ?? 'Sistema')[0] }}</span>
                                            </div>
                                            <div>
                                                <span class="badge rounded-pill px-3 py-2 {{ ($jogo->limite_jogadores - $jogo->inscricoes_count) > 0 ? 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25' : 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' }}">
                                                    {{ $jogo->limite_jogadores - $jogo->inscricoes_count }} vagas livres
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Ações de Inscrição --}}
                                        <div class="mt-auto pt-3 border-top border-opacity-50">
                                            @php
                                                $minhaInscricao = $jogo->inscricoes->first(); 
                                            @endphp

                                            @if(!$minhaInscricao)
                                                {{-- Não inscrito --}}
                                                <form action="{{ route('fazer_inscricao') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="jogo_id" value="{{ Crypt::encrypt($jogo->id) }}">
                                                    <button type="submit" class="btn btn-outline-success w-100 btn-action-pira">
                                                        <i class="bi bi-plus-circle me-1"></i> Quero Participar
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Já inscrito --}}
                                                <div class="text-center">
                                                    @if($minhaInscricao->status == 'pendente')
                                                        <div class="bg-warning bg-opacity-10 border border-warning rounded-4 py-2 mb-2 fw-bold small">
                                                            <i class="bi bi-clock-history me-1"></i> Aguardando aprovação
                                                        </div>
                                                    @elseif($minhaInscricao->status == 'confirmado')
                                                        <div class="bg-success bg-opacity-10 text-success border border-success rounded-4 py-2 mb-2 fw-bold small">
                                                            <i class="bi bi-check-circle-fill me-1"></i> Incrição Confirmada!
                                                        </div>
                                                    @elseif($minhaInscricao->status == 'cancelada')
                                                        <div class="bg-danger bg-opacity-10 text-danger border border-danger rounded-4 py-2 mb-2 fw-bold small">
                                                            <i class="bi bi-x-circle-fill me-1"></i> Inscrição Cancelada
                                                        </div>
                                                        <form action="{{ route('fazer_inscricao') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="jogo_id" value="{{ Crypt::encrypt($jogo->id) }}">
                                                            <button type="submit" class="btn btn-outline-secondary w-100 btn-action-pira" style="font-size: 0.85rem;">
                                                                Tentar novamente
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($minhaInscricao->status != 'cancelada' && $minhaInscricao->status != 'recusada')
                                                        <form action="{{ route('cancelar_inscricao') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar sua vaga?')">
                                                            @csrf
                                                            <input type="hidden" name="inscricao_id" value="{{ Crypt::encrypt($minhaInscricao->id) }}">
                                                            <button type="submit" class="btn text-danger bg-transparent p-0 mt-2 small fw-bold text-decoration-underline" style="border: none;">
                                                                Cancelar minha vaga
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