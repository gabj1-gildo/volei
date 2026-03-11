@extends('layouts.main_layout')

@section('content')

    {{-- mensagem de sucesso na ativação ou desativação de locais--}}
    <div class="toast-container position-fixed top-0 end-0 p-3 mt-5" style="z-index: 1080">
        <div id="toastSucesso" class="toast align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Gerenciamento de Locais</h5>
                <button class="btn btn-sm btn-light fw-bold" data-bs-toggle="modal" data-bs-target="#modalNovoLocal">
                    <i class="bi bi-plus-lg me-1"></i> Novo Local
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4">NOME</th>
                            <th>ENDEREÇO</th>
                            <th>TIPO</th>
                            <th class="text-center">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locais as $local)
                        <tr class="{{ !$local->ativo ? 'bg-light' : '' }}">
                            <td class="ps-4">
                                <span class="fw-bold {{ !$local->ativo ? 'text-decoration-line-through text-muted opacity-50' : '' }}">
                                    {{ $local->nome }}
                                </span>
                                @if(!$local->ativo)
                                    <span class="badge bg-danger ms-2" style="font-size: 0.7rem;">INATIVO</span>
                                @endif
                            </td>
                            <td class="{{ !$local->ativo ? 'opacity-50' : '' }}">{{ $local->endereco }}</td>
                            <td class="{{ !$local->ativo ? 'opacity-50' : '' }}">
                                <span class="badge {{ $local->tipo === 'publico' ? 'bg-info text-dark' : 'bg-warning text-dark' }}">
                                    {{ strtoupper($local->tipo) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    {{-- Editar: O botão de editar continua podendo ser desabilitado, mas não ficará mais opaco por herança da linha --}}
                                    <button class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarLocal"
                                            data-id="{{ $local->id }}"
                                            data-nome="{{ $local->nome }}"
                                            data-endereco="{{ $local->endereco }}"
                                            data-tipo="{{ $local->tipo }}"
                                            {{ !$local->ativo ? 'disabled' : '' }}>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    
                                    {{-- Botão Alternar Status: Agora sempre terá 100% de opacidade --}}
                                    <form action="{{ route('alternar_status_local') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $local->id }}">
                                        @if($local->ativo)
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Desativar">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Reativar">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Nenhum local cadastrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Novo Local --}}
    <div class="modal fade" id="modalNovoLocal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('salvar_local') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Cadastrar Novo Local</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do Local</label>
                            <input type="text" name="nome" class="form-control" placeholder="Ex: Quadra do Sesc" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Endereço</label>
                            <input type="text" name="endereco" class="form-control" placeholder="Rua, Bairro..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo</label>
                            <select name="tipo" class="form-select" required>
                                <option value="publico">Público</option>
                                <option value="privado">Privado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Local</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Editar Local --}}
    <div class="modal fade" id="modalEditarLocal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('atualizar_local') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="edit-id">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Editar Local</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome</label>
                            <input type="text" name="nome" id="edit-nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Endereço</label>
                            <input type="text" name="endereco" id="edit-endereco" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo</label>
                            <select name="tipo" id="edit-tipo" class="form-select" required>
                                <option value="publico">Público</option>
                                <option value="privado">Privado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-success">Atualizar Dados</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEditar = document.getElementById('modalEditarLocal');
            if (modalEditar) {
                modalEditar.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    modalEditar.querySelector('#edit-id').value = button.getAttribute('data-id');
                    modalEditar.querySelector('#edit-nome').value = button.getAttribute('data-nome');
                    modalEditar.querySelector('#edit-endereco').value = button.getAttribute('data-endereco');
                    modalEditar.querySelector('#edit-tipo').value = button.getAttribute('data-tipo');
                });
            }
            @if(session('success'))
                const toastEl = document.getElementById('toastSucesso');
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            @endif
        });
        
    </script>
@endsection