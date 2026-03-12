@extends('layouts.main_layout')

@section('content')

    <style>
        /* Estilização Premium para os Cartões de Jogo */
        .game-card {
            border-radius: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            background-color: #ffffff;
            overflow: hidden;
            position: relative;
        }

        .game-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.08);
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
            padding: 0.5rem 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .card-cancelled {
            opacity: 0.7;
            background-color: #f8f9fa;
        }
        
        [data-bs-theme="dark"] .card-cancelled {
            background-color: #1a1d21;
        }

        .organizer-header {
            display: inline-block;
            /* background: linear-gradient(135deg, #212529 0%, #343a40 100%); */
            /* color: white; */
            padding: 0.5rem 1.5rem;
            border-radius: 2rem;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.308);
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
        {{-- Cabeçalho Principal da Página --}}
        <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-gear-fill me-2 text-primary"></i>Gerenciamento de Jogos</h4>
            <button class="btn btn-primary fw-bold shadow-sm btn-action-pira px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalNovoJogo">
                <i class="bi bi-plus-lg me-1"></i> Novo Jogo
            </button>
        </div>

        <div class="row justify-content-center">
            <div class="col">
                
                {{-- Verifica se existem jogos --}}
                @if($jogosAgrupados->isEmpty())
                    <div class="card shadow-sm border-0 p-5 text-center" style="border-radius: 1.5rem;">
                        <div class="fs-1 mb-3">🏐</div>
                        <h4 class="text-secondary fw-bold mb-0">Nenhum jogo cadastrado no momento.</h4>
                        <p class="text-muted mt-2">Clique em "Novo Jogo" para criar a primeira partida.</p>
                    </div>
                @else
                    
                    {{-- Laço que cria uma Seção para CADA Organizador --}}
                    @foreach($jogosAgrupados as $organizador => $jogos)
                        <div class="mb-5">
                            
                            {{-- Título do Organizador --}}
                            <div class="mb-4">
                                <div class="organizer-header">
                                    <i class="bi bi-person-circle text-warning me-2"></i> Responsável: {{ $organizador }}
                                </div>
                            </div>

                            {{-- Grid de Cartões exclusiva deste organizador --}}
                            <div class="row g-4">
                                @foreach($jogos as $jogo)
                                    @php
                                        $isCancelado = $jogo->status === 'cancelado';
                                    @endphp
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100 game-card {{ $isCancelado ? 'card-cancelled' : '' }}">
                                            <div class="card-body p-4 d-flex flex-column">
                                                
                                                {{-- Cabeçalho do Cartão (Título) --}}
                                                <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom border-opacity-50">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                                                            <i class="bi bi-trophy-fill fs-5"></i>
                                                        </div>
                                                        <h4 class="card-title mb-0 fw-bold {{ $isCancelado ? 'text-decoration-line-through text-muted' : '' }}" style="font-size: 1.2rem; line-height: 1.2;">
                                                            {{ $jogo->titulo->nome ?? 'Sem Título' }}
                                                        </h4>
                                                    </div>
                                                </div>
                                                
                                                {{-- Informações do Jogo --}}
                                                <div class="d-flex flex-column gap-2 mb-4">
                                                    <div class="d-flex align-items-start">
                                                        <i class="bi bi-calendar-event text-secondary mt-1 me-2"></i>
                                                        <div>
                                                            <span class="d-block small text-muted fw-bold">DATA / HORA</span>
                                                            <span class="fw-medium">{{ date('d/m/Y \à\s H:i', strtotime($jogo->data_hora)) }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-start">
                                                        <i class="bi bi-geo-alt text-secondary mt-1 me-2"></i>
                                                        <div>
                                                            <span class="d-block small text-muted fw-bold">LOCAL</span>
                                                            <span class="fw-medium">{{ $jogo->local->nome ?? 'Sem Local' }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-start">
                                                        <i class="bi bi-info-circle text-secondary mt-1 me-2"></i>
                                                        <div>
                                                            <span class="d-block small text-muted fw-bold">STATUS</span>
                                                            @php
                                                                $badgeClass = match($jogo->status) {
                                                                    'aberto' => 'bg-success',
                                                                    'inscricoes_encerradas' => 'bg-warning text-dark',
                                                                    'cancelado' => 'bg-danger',
                                                                    'encerrado' => 'bg-secondary',
                                                                    default => 'bg-light text-dark'
                                                                };
                                                            @endphp
                                                            <span class="badge {{ $badgeClass }} mt-1">
                                                                {{ strtoupper(str_replace('_', ' ', $jogo->status)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- Indicador de Vagas --}}
                                                <div class="d-flex justify-content-between align-items-center mb-4 p-2 bg-light rounded-3 border">
                                                    <span class="small fw-bold text-muted"><i class="bi bi-people-fill me-1"></i> Ocupação:</span>
                                                    <span class="badge rounded-pill px-3 py-2 bg-info text-dark border border-info border-opacity-25 shadow-sm">
                                                        {{ $jogo->limite_jogadores - $jogo->inscricoes_count }} / {{ $jogo->limite_jogadores }} vagas
                                                    </span>
                                                </div>

                                                {{-- Ações de Gerenciamento --}}
                                                <div class="mt-auto pt-3 border-top border-opacity-50 d-flex gap-2 justify-content-center">
                                                    
                                                    {{-- Editar --}}
                                                    <button class="btn btn-outline-primary flex-fill btn-action-pira" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalEditarJogo"
                                                            data-id="{{ $jogo->id }}"
                                                            data-titulo="{{ $jogo->titulo_id ?? '' }}"
                                                            data-local="{{ $jogo->local_id ?? '' }}"
                                                            data-data="{{ date('Y-m-d', strtotime($jogo->data_hora)) }}"
                                                            data-hora="{{ date('H:i', strtotime($jogo->data_hora)) }}"
                                                            data-limite="{{ $jogo->limite_jogadores }}"
                                                            data-data-limite="{{ date('Y-m-d', strtotime($jogo->data_hora_limite_inscricao)) }}"
                                                            data-hora-limite="{{ date('H:i', strtotime($jogo->data_hora_limite_inscricao)) }}"
                                                            data-descricao="{{ $jogo->descricao ?? '' }}"
                                                            data-responsavel="{{ $jogo->user_id ?? '' }}"
                                                            title="Editar Jogo"
                                                            {{ in_array($jogo->status, ['cancelado', 'encerrado']) ? 'disabled' : '' }}>
                                                        <i class="bi bi-pencil"></i>
                                                    </button>

                                                    {{-- Inscrições --}}
                                                    <a href="{{ route('gerenciar_inscricoes', ['jogo' => $jogo->id]) }}" 
                                                       class="btn btn-outline-info flex-fill btn-action-pira {{ in_array($jogo->status, ['cancelado', 'encerrado']) ? 'disabled' : '' }}" 
                                                       title="Ver Inscrições"
                                                       {{ in_array($jogo->status, ['cancelado', 'encerrado']) ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                                                        <i class="bi bi-list-check"></i>
                                                    </a>
                                                    
                                                    {{-- Status (Cancelar / Reativar) --}}
                                                    @if($jogo->status === 'encerrado')
                                                        <button class="btn btn-outline-secondary flex-fill btn-action-pira" disabled title="Partida Encerrada">
                                                            <i class="bi bi-lock-fill"></i>
                                                        </button>
                                                    @elseif($jogo->status === 'cancelado')
                                                        <form action="{{ route('alterar_status_partida') }}" method="POST" class="m-0 flex-fill d-flex" onsubmit="return confirm('Deseja reativar esta partida?')">
                                                            @csrf
                                                            <input type="hidden" name="id_partida" value="{{ $jogo->id }}">
                                                            <input type="hidden" name="status" value="aberto">
                                                            <button type="submit" class="btn btn-outline-success w-100 btn-action-pira" title="Reativar Partida">
                                                                <i class="bi bi-arrow-clockwise"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('alterar_status_partida') }}" method="POST" class="m-0 flex-fill d-flex" onsubmit="return confirm('Confirmar cancelamento da partida?')">
                                                            @csrf
                                                            <input type="hidden" name="id_partida" value="{{ $jogo->id }}">
                                                            <input type="hidden" name="status" value="cancelado">
                                                            <button type="submit" class="btn btn-outline-danger w-100 btn-action-pira" title="Cancelar Partida">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL NOVO JOGO --}}
    <div class="modal fade" id="modalNovoJogo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('salvar_jogo') }}" method="POST">
                    @csrf

                    @php
                        $hoje = date('Y-m-d');
                    @endphp

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
                                <input type="date" name="data" id="data_jogo_novo" min="{{ $hoje }}" 
                                    class="form-control @error('data') is-invalid @enderror"
                                    value="{{ old('data', $hoje) }}" required>

                                @error('data') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Hora</label>
                                <input type="time" name="hora" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Limite de Vagas</label>
                                <input type="number" name="limite_jogadores" class="form-control" min="4" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-warning">Data Limite Inscrição</label>
                                <input type="date" name="data_limite_inscricao" id="data_limite_novo" min="{{ $hoje }}"
                                    class="form-control border-warning-subtle @error('data_limite_inscricao') is-invalid @enderror" 
                                    value="{{ old('data_limite_inscricao', $hoje) }}" required>

                                @error('data_limite_inscricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-warning">Hora Limite</label>
                                <input type="time" name="hora_limite_inscricao" class="form-control border-warning-subtle" required>
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
                                <textarea name="descricao" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Salvar Partida</button>
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

                    @php
                        $hoje = date('Y-m-d');
                    @endphp

                    <input type="hidden" name="id" id="edit-id" value="{{ old('id') }}">
                    
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">Editar Partida</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            {{-- Título --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Título</label>
                                <select name="titulo" id="edit-titulo" class="form-select @error('titulo') is-invalid @enderror" required>
                                    @foreach($titulos as $t) 
                                        <option value="{{$t->id}}" {{ old('titulo') == $t->id ? 'selected' : '' }}>{{$t->nome}}</option> 
                                    @endforeach
                                </select>
                                @error('titulo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Local --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Local</label>
                                <select name="local" id="edit-local" class="form-select @error('local') is-invalid @enderror" required>
                                    @foreach($locais as $l) 
                                        <option value="{{$l->id}}" {{ old('local') == $l->id ? 'selected' : '' }}>{{$l->nome}}</option> 
                                    @endforeach
                                </select>
                                @error('local') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Data do Jogo --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-primary">Data do Jogo</label>
                                <input type="date" name="data" id="edit-data" 
                                    class="form-control @error('data') is-invalid @enderror" 
                                    min="{{ $hoje }}" value="{{ old('data') }}" required>
                                @error('data') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Hora do Jogo --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-primary">Hora</label>
                                <input type="time" name="hora" id="edit-hora" 
                                    class="form-control @error('hora') is-invalid @enderror" 
                                    value="{{ old('hora') }}" required>
                                @error('hora') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Vagas --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Vagas</label>
                                <input type="number" name="limite_jogadores" id="edit-limite" 
                                    class="form-control @error('limite_jogadores') is-invalid @enderror" 
                                    min="1" value="{{ old('limite_jogadores') }}" required>
                                @error('limite_jogadores') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Data Limite Inscrição --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-warning">Data Limite Inscrição</label>
                                <input type="date" name="data_limite_inscricao" id="edit-data-limite" 
                                    class="form-control border-warning-subtle @error('data_limite_inscricao') is-invalid @enderror" 
                                    min="{{ $hoje }}" value="{{ old('data_limite_inscricao') }}" required>
                                @error('data_limite_inscricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Hora Limite Inscrição --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-warning">Hora Limite</label>
                                <input type="time" name="hora_limite_inscricao" id="edit-hora-limite" 
                                    class="form-control border-warning-subtle @error('hora_limite_inscricao') is-invalid @enderror" 
                                    value="{{ old('hora_limite_inscricao') }}" required>
                                @error('hora_limite_inscricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Responsável (Admin) --}}
                            @if(auth()->user()->tipo === 'admin')
                                <div class="col-12">
                                    <label class="form-label fw-bold small">Responsável</label>
                                    <select name="responsavel_id" id="edit-responsavel" class="form-select bg-light">
                                        <option value="{{ auth()->id() }}">Eu mesmo ({{auth()->user()->name}})</option>
                                        @foreach($organizadores as $org) 
                                            <option value="{{ $org->id }}" {{ old('responsavel_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option> 
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Descrição --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small">Descrição</label>
                                <textarea name="descricao" id="edit-descricao" class="form-control @error('descricao') is-invalid @enderror" rows="3" required>{{ old('descricao') }}</textarea>
                                @error('descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 fw-bold">Atualizar Partida</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataHoje = "{{ date('Y-m-d') }}";

            // --- INÍCIO DA PARTE MANTIDA INTACTA ---
            // Script para preencher o Modal de Edição
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

                    // DISPARO DE EVENTO PARA ATUALIZAR REGRAS AO ABRIR EDIÇÃO
                    modalEditarJogo.querySelector('#edit-data').dispatchEvent(new Event('change'));
                });
            }

            // Exibir Toast se houver mensagem
            @if(session('success') || session('error'))
                const toastEl = document.getElementById('toastAlerta');
                if (toastEl) {
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                }
            @endif
            // --- FIM DA PARTE MANTIDA INTACTA ---

            // --- LÓGICA DE CONTROLE DE HORÁRIOS (NOVO E EDITAR) ---
            function aplicarRegrasDataHora(idData, idHora, idDataLimite) {
                const inputData = document.getElementById(idData);
                const inputHora = document.getElementById(idHora);
                const inputLimite = document.getElementById(idDataLimite);

                if (!inputData || !inputHora) return;

                function validar() {
                    // Se for hoje, a hora mínima é a atual
                    if (inputData.value === dataHoje) {
                        const agora = new Date();
                        const horaMin = agora.getHours().toString().padStart(2, '0') + ":" + 
                                    agora.getMinutes().toString().padStart(2, '0');
                        inputHora.min = horaMin;
                    } else {
                        inputHora.removeAttribute('min');
                    }

                    // Sincroniza o limite de inscrição com a data do jogo
                    if (inputLimite) {
                        inputLimite.max = inputData.value;
                    }
                }

                inputData.addEventListener('change', validar);
                validar();
            }

            // Ativa para os IDs do Modal Novo (ajuste os IDs conforme seu HTML)
            aplicarRegrasDataHora('data_jogo_novo', 'hora_jogo_novo', 'data_limite_novo');

            // Ativa para os IDs do Modal Editar
            aplicarRegrasDataHora('edit-data', 'edit-hora', 'edit-data-limite');

            // --- PERSISTÊNCIA DOS MODAIS EM CASO DE ERRO DE VALIDAÇÃO ---
            @if($errors->any())
                const modalAlvo = "{{ old('id') ? 'modalEditarJogo' : 'modalNovoJogo' }}";
                const element = document.getElementById(modalAlvo);
                if (element) {
                    const myModal = new bootstrap.Modal(element);
                    myModal.show();
                }
            @endif
        });
    </script>
@endsection