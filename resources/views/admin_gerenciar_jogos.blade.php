@extends('layouts.main_layout')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-calendar-event text-primary me-2"></i>Gerenciamento de Jogos</h2>
        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNovoJogo">
            <i class="bi bi-plus-lg me-1"></i> Novo Jogo
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="accordion shadow-sm" id="accordionJogos">
        @forelse($jogosAgrupados as $organizador => $jogos)
            <div class="accordion-item mb-2 border-0 overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($organizador) }}">
                        <i class="bi bi-person-circle me-2 text-secondary"></i> {{ $organizador }} ({{ $jogos->count() }} jogos)
                    </button>
                </h2>
                <div id="collapse{{ Str::slug($organizador) }}" class="accordion-collapse collapse" data-bs-parent="#accordionJogos">
                    <div class="accordion-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4">TÍTULO</th>
                                    <th>DATA/HORA</th>
                                    <th>LOCAL</th>
                                    <th class="text-center">VAGAS</th>
                                    <th class="text-center">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jogos as $jogo)
                                <tr>
                                    <td class="ps-4"><strong>{{ $jogo->titulo->nome }}</strong></td>
                                    <td>{{ date('d/m H:i', strtotime($jogo->data_hora)) }}</td>
                                    <td>{{ $jogo->local->nome }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark">{{ $jogo->inscricoes_count }} / {{ $jogo->vagas }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('gerenciar_inscricoes', ['jogo' => $jogo->id]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-people"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center py-5 text-muted">Nenhum jogo cadastrado.</p>
        @endforelse
    </div>
</div>

<div class="modal fade" id="modalNovoJogo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('salvar_jogo') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold">Cadastrar Partida</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Título da Partida</label>
                        <select name="titulo" class="form-select" required>
                            @foreach($titulos as $t) <option value="{{$t->id}}">{{$t->nome}}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Local</label>
                        <select name="local" class="form-select" required>
                            @foreach($locais as $l) <option value="{{$l->id}}">{{$l->nome}}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Data do Jogo</label>
                        <input type="date" name="data" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Hora</label>
                        <input type="time" name="hora" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Limite de Vagas</label>
                        <input type="number" name="limite_jogadores" class="form-control" min="1" required>
                    </div>

                    {{-- data limite de inscrição, hora limite de inscrição e responsavel (se for organizador é quem está cadastrando se for admin pode ser ele ou qualquer organizador) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Data Limite de Inscrição</label>
                        <input type="date" name="data_limite_inscricao" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Hora Limite de Inscrição</label>  
                        <input type="time" name="hora_limite_inscricao" class="form-control" required>
                    </div>
                    
                    <div class="row g-3">
                    {{-- Seleção de Responsável: Somente para ADMIN --}}
                    @if(auth()->user()->tipo === 'admin')
                    <div class="col-12">
                        <label class="form-label fw-bold">Responsável pela Partida</label>
                        <select name="responsavel_id" class="form-select shadow-sm" required>
                            <option value="{{ auth()->id() }}">Eu mesmo ({{auth()->user()->name}})</option>
                            @foreach($organizadores as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-secondary border-0 small text-warning">
                        <i class="bi bi-info-circle me-1"></i> Como administrador, você pode definir quem será o organizador responsável por esta partida.
                    </div>
                    @elseif(auth()->user()->tipo === 'organizador')
                        <div class="col-12">
                            <label class="form-label fw-bold">Responsável pela Partida</label>
                            {{-- Campo visível apenas para leitura --}}
                            <input type="text" class="form-control bg-light" style="cursor: not-allowed" value="{{ auth()->user()->name }}" readonly disabled>
                            {{-- Campo oculto que realmente envia o ID para o Controller --}}
                            <input type="hidden" name="responsavel_id" value="{{ auth()->id() }}">
                        </div>
                    @endif

                    <div class="col-12 mt-3">
                        <label class="form-label fw-bold">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3" placeholder="Ex: Levar Água..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-1 px-3" style="min-height: auto;">
                <button type="button" class="btn btn-sm btn-secondary fw-bold" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-sm btn-success px-4 fw-bold">
                    Publicar Jogo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection