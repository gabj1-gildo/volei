@extends('layouts.main_layout')

@section('content')

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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold text-dark">
                <i class="bi bi-people-fill me-2 text-primary"></i>Inscrições Recebidas
            </h4>
            <span class="badge bg-primary text-white shadow-sm px-3 py-2 rounded-pill">Painel do Organizador</span>
        </div>

        {{-- Verifica se existem jogos para exibir --}}
        @if($jogos->isEmpty())
            <div class="card shadow-sm border-0 p-5 text-center" style="border-radius: 1rem;">
                <h5 class="text-muted mb-0">Nenhum jogo ativo encontrado para gerenciar inscrições.</h5>
            </div>
        @else
            
            {{-- Laço que cria um Cartão completo para CADA Jogo --}}
            @foreach($jogos as $jogo)
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 1rem; overflow: hidden;">
                    
                    {{-- Cabeçalho do Cartão (Dados do Jogo) --}}
                    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-uppercase">
                            <i class="bi bi-controller me-2 text-warning"></i> {{ $jogo->titulo->nome ?? 'Sem Título' }}
                        </h6>
                        <div class="small fw-medium">
                            <i class="bi bi-person-badge ms-3 me-1"></i> {{ $jogo->responsavel->name ?? 'Organizador Indefinido' }}
                            <i class="bi bi-calendar-event ms-3 me-1"></i> {{ date('d/m/Y H:i', strtotime($jogo->data_hora)) }}
                            <i class="bi bi-geo-alt ms-3 me-1"></i> {{ $jogo->local->nome ?? 'Local Indefinido' }}
                        </div>
                    </div>

                    {{-- Tabela de Inscrições do Jogo --}}
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4">JOGADOR</th>
                                    <th>STATUS</th>
                                    <th class="text-center">AÇÕES DE GERENCIAMENTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jogo->inscricoes as $inscricao)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center me-3 border" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person-fill fs-5"></i>
                                                </div>
                                                <span class="fw-bold">{{ $inscricao->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($inscricao->status) {
                                                    'confirmado' => 'bg-success',
                                                    'pendente'   => 'bg-warning text-dark',
                                                    'cancelada'  => 'bg-danger',
                                                    default      => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ strtoupper($inscricao->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('alterar_status_inscricao') }}" method="POST" class="m-0 d-flex gap-2 justify-content-center">
                                                @csrf
                                                <input type="hidden" name="id_inscricao" value="{{ $inscricao->id }}">
                                                
                                                {{-- Botão Aprovar --}}
                                                <button type="submit" name="status" value="confirmado" class="btn btn-sm btn-outline-success fw-bold" {{ $inscricao->status === 'confirmado' ? 'disabled' : '' }}>
                                                    <i class="bi bi-check-lg"></i> Aprovar
                                                </button>

                                                {{-- Botão Recusar/Cancelar --}}
                                                <button type="submit" name="status" value="cancelada" class="btn btn-sm btn-outline-danger fw-bold" {{ $inscricao->status === 'cancelada' ? 'disabled' : '' }}>
                                                    <i class="bi bi-x-lg"></i> Recusar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-secondary">
                                            <i class="bi bi-inbox fs-4 d-block mb-2 text-muted"></i>
                                            Nenhuma inscrição registrada para este jogo até o momento.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Script para ativar o Toast automaticamente --}}
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