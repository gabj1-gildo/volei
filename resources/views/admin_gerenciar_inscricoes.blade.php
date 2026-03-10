@extends('layouts.main_layout')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm border-0">
        {{-- Cabeçalho seguindo o padrão de Locais/Jogos --}}
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Inscrições Recebidas</h5>
            <span class="badge bg-light text-dark fw-bold">Painel do Organizador</span>
        </div>

        @if(session('success'))
            <div class="alert alert-success m-3 border-0 shadow-sm text-center">{{ session('success') }}</div>
        @endif

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
                    @forelse($jogos as $jogo)
                        {{-- Separador por Jogo --}}
                        <tr class="table-secondary">
                            <td colspan="3" class="ps-4 fw-bold small text-uppercase">
                                <i class="bi bi-controller me-1"></i> {{ $jogo->titulo->nome }} 
                                <span class="text-muted ms-2">({{ date('d/m', strtotime($jogo->data_hora)) }})</span>
                            </td>
                        </tr>

                        @forelse($jogo->inscricoes as $inscricao)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2 text-secondary fs-5"></i>
                                        <strong>{{ $inscricao->user->name }}</strong>
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
                                    <div class="d-flex gap-2 justify-content-center">
                                        <form action="{{ route('alterar_status_inscricao') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id_inscricao" value="{{ $inscricao->id }}">
                                            
                                            {{-- Botão Aprovar --}}
                                            @if($inscricao->status !== 'confirmado')
                                                <button name="status" value="confirmado" class="btn btn-sm btn-success shadow-sm">
                                                    <i class="bi bi-check-lg"></i> Aprovar
                                                </button>
                                            @endif

                                            {{-- Botão Recusar --}}
                                            @if($inscricao->status !== 'cancelada')
                                                <button name="status" value="cancelada" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-x-lg"></i> Recusar
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-danger medium">Nenhuma inscrição registrada para este jogo.</td>
                            </tr>
                        @endforelse
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-danger">Nenhum jogo ativo encontrado para gerenciar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection