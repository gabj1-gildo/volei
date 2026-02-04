@extends('layouts.main_layout')

@section('content')
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
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Endereço</th>
                        <th>Tipo</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locais as $local)
                    <tr>
                        <td class="fw-bold">{{ $local->nome }}</td>
                        <td>{{ $local->endereco }}</td>
                        <td>
                            <span class="badge {{ $local->tipo === 'publico' ? 'bg-info text-dark' : 'bg-warning text-dark' }}">
                                {{ strtoupper($local->tipo) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarLocal"
                                        data-id="{{ $local->id }}"
                                        data-nome="{{ $local->nome }}"
                                        data-endereco="{{ $local->endereco }}"
                                        data-tipo="{{ $local->tipo }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                <form action="{{ route('excluir_local') }}" method="POST" onsubmit="return confirm('Excluir este local?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $local->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Nenhum local cadastrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

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
                        <input type="text" name="nome" class="form-control" placeholder="Ex: Ginásio Central" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Endereço</label>
                        <input type="text" name="endereco" class="form-control" placeholder="Rua, Número, Bairro" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Local</label>
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
                
                // Extração dos dados
                const id = button.getAttribute('data-id');
                const nome = button.getAttribute('data-nome');
                const endereco = button.getAttribute('data-endereco');
                const tipo = button.getAttribute('data-tipo');

                // Preenchimento dos campos
                modalEditar.querySelector('#edit-id').value = id;
                modalEditar.querySelector('#edit-nome').value = nome;
                modalEditar.querySelector('#edit-endereco').value = endereco;
                
                // A mágica da pré-seleção do select acontece aqui:
                modalEditar.querySelector('#edit-tipo').value = tipo;
            });
        }
    });
</script>
@endsection