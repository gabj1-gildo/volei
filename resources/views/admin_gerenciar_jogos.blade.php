@extends('layouts.main_layout')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-sm border-0">
            {{-- Cabeçalho no estilo da página de Locais --}}
            <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-event-fill me-2"></i>Gerenciamento de Jogos</h5>
                <button class="btn btn-sm btn-light fw-bold" data-bs-toggle="modal" data-bs-target="#modalNovoJogo">
                    <i class="bi bi-plus-lg me-1"></i> Novo Jogo
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success m-3 border-0 shadow-sm text-center">{{ session('success') }}</div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger m-3 border-0 shadow-sm text-center">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4">TÍTULO</th>
                            <th>DATA/HORA</th>
                            <th>LOCAL</th>
                            <th>STATUS</th>
                            <th class="text-center">VAGAS</th>
                            <th class="text-center">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jogosAgrupados as $organizador => $jogos)
                            <tr class="table-secondary">
                                <td colspan="6" class="ps-4 fw-bold small text-uppercase text-secondary">
                                    <i class="bi bi-person-circle me-1"></i> {{ $organizador }}
                                </td>
                            </tr>
                            @foreach($jogos as $jogo)
                            <tr class="{{ $jogo->status === 'cancelado' ? 'opacity-50' : '' }}">
                                <td class="ps-4">
                                    <strong class="{{ $jogo->status === 'cancelado' ? 'text-decoration-line-through text-muted' : '' }}">
                                        {{ $jogo->titulo->nome }}
                                    </strong>
                                </td>
                                <td>{{ date('d/m H:i', strtotime($jogo->data_hora)) }}</td>
                                <td>{{ $jogo->local->nome }}</td>
                                <td>
                                    <span class="badge {{ $jogo->status === 'aberto' ? 'bg-success' : 'bg-danger' }}">
                                        {{ strtoupper($jogo->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark">
                                        {{ $jogo->limite_jogadores - $jogo->inscricoes_count }} / {{ $jogo->limite_jogadores }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        {{-- Botão Editar --}}
                                        <button class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditarJogo"
                                                data-id="{{ $jogo->id }}"
                                                data-titulo="{{ $jogo->titulo_id }}"
                                                data-local="{{ $jogo->local_id }}"
                                                data-data="{{ date('Y-m-d', strtotime($jogo->data_hora)) }}"
                                                data-hora="{{ date('H:i', strtotime($jogo->data_hora)) }}"
                                                data-limite="{{ $jogo->limite_jogadores }}"
                                                data-data-limite="{{ date('Y-m-d', strtotime($jogo->data_hora_limite_inscricao)) }}"
                                                data-hora-limite="{{ date('H:i', strtotime($jogo->data_hora_limite_inscricao)) }}"
                                                data-responsavel="{{ $jogo->user_id }}"
                                                data-descricao="{{ $jogo->descricao }}"
                                                {{ $jogo->status === 'cancelado' ? 'disabled' : '' }}>
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Link para Inscrições --}}
                                        <a href="{{ route('gerenciar_inscricoes', ['jogo' => $jogo->id]) }}" 
                                        class="btn btn-sm btn-outline-info" title="Ver Inscrições">
                                            <i class="bi bi-people"></i>
                                        </a>

                                        {{-- Botão Cancelar --}}
                                        @if($jogo->status === 'aberto')
                                            <form action="{{ route('cancelar_jogo') }}" method="POST" onsubmit="return confirm('Confirmar cancelamento da partida?')">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $jogo->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancelar Partida">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Nenhum jogo cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL NOVO JOGO --}}
    <div class="modal fade" id="modalNovoJogo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('salvar_jogo') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold">Cadastrar Nova Partida</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Título</label>
                                <select name="titulo" class="form-select" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    @foreach($titulos as $t) <option value="{{$t->id}}">{{$t->nome}}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Local</label>
                                <select name="local" class="form-select" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    @foreach($locais as $l) <option value="{{$l->id}}">{{$l->nome}}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Data</label>
                                <input type="date" name="data" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Hora</label>
                                <input type="time" name="hora" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Limite de Vagas</label>
                                <input type="number" name="limite_jogadores" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-danger">Data Limite Inscrição</label>
                                <input type="date" name="data_limite_inscricao" class="form-control border-danger-subtle" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-danger">Hora Limite</label>
                                <input type="time" name="hora_limite_inscricao" class="form-control border-danger-subtle" required>
                            </div>
                            @if(auth()->user()->tipo === 'admin')
                            <div class="col-12">
                                <label class="form-label fw-bold small">Responsável</label>
                                <select name="responsavel_id" class="form-select bg-light" required>
                                    <option value="{{ auth()->id() }}">Eu mesmo ({{auth()->user()->name}})</option>
                                    @foreach($organizadores as $org) <option value="{{ $org->id }}">{{ $org->name }}</option> @endforeach
                                </select>
                            </div>
                            @else
                                <input type="hidden" name="responsavel_id" value="{{ auth()->id() }}">
                            @endif
                            <div class="col-12">
                                <label class="form-label fw-bold small">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4">Salvar Partida</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDITAR JOGO --}}
    <div class="modal fade" id="modalEditarJogo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('atualizar_jogo') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="edit-id">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">Editar Partida</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Título</label>
                                <select name="titulo" id="edit-titulo" class="form-select" required>
                                    @foreach($titulos as $t) <option value="{{$t->id}}">{{$t->nome}}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Local</label>
                                <select name="local" id="edit-local" class="form-select" required>
                                    @foreach($locais as $l) <option value="{{$l->id}}">{{$l->nome}}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Data</label>
                                <input type="date" name="data" id="edit-data" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Hora</label>
                                <input type="time" name="hora" id="edit-hora" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Vagas</label>
                                <input type="number" name="limite_jogadores" id="edit-limite" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Data Limite Inscrição</label>
                                <input type="date" name="data_limite_inscricao" id="edit-data-limite" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Hora Limite</label>
                                <input type="time" name="hora_limite_inscricao" id="edit-hora-limite" class="form-control" required>
                            </div>
                            @if(auth()->user()->tipo === 'admin')
                            <div class="col-12">
                                <label class="form-label fw-bold small">Responsável</label>
                                <select name="responsavel_id" id="edit-responsavel" class="form-select bg-light">
                                    @foreach($organizadores as $org) <option value="{{ $org->id }}">{{ $org->name }}</option> @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-12">
                                <label class="form-label fw-bold small">Descrição</label>
                                <textarea name="descricao" id="edit-descricao" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success btn-sm px-4">Atualizar Partida</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEditarJogo = document.getElementById('modalEditarJogo');
            if (modalEditarJogo) {
                modalEditarJogo.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    
                    // Preenchimento automático dos campos
                    modalEditarJogo.querySelector('#edit-id').value = button.getAttribute('data-id');
                    modalEditarJogo.querySelector('#edit-titulo').value = button.getAttribute('data-titulo');
                    modalEditarJogo.querySelector('#edit-local').value = button.getAttribute('data-local');
                    modalEditarJogo.querySelector('#edit-data').value = button.getAttribute('data-data');
                    modalEditarJogo.querySelector('#edit-hora').value = button.getAttribute('data-hora');
                    modalEditarJogo.querySelector('#edit-limite').value = button.getAttribute('data-limite');
                    modalEditarJogo.querySelector('#edit-data-limite').value = button.getAttribute('data-data-limite');
                    modalEditarJogo.querySelector('#edit-hora-limite').value = button.getAttribute('data-hora-limite');
                    modalEditarJogo.querySelector('#edit-descricao').value = button.getAttribute('data-descricao');

                    // Responsável (campo presente apenas se admin)
                    const campoResp = modalEditarJogo.querySelector('#edit-responsavel');
                    if(campoResp) campoResp.value = button.getAttribute('data-responsavel');
                });
            }
        });
    </script>
@endsection