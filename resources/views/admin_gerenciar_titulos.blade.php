@extends('layouts.main_layout')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-bookmark-plus me-2"></i>Gerenciamento de Títulos</h5>
            <button class="btn btn-sm btn-light fw-bold" data-bs-toggle="modal" data-bs-target="#modalNovoTitulo">
                <i class="bi bi-plus-lg me-1"></i> Cadastrar Título
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70%">Título</th>
                        <th class="text-center" style="width: 30%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($titulos as $titulo)
                    <tr>
                        <td><span class="fw-bold">{{ $titulo->nome }}</span></td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarTitulo"
                                        data-id="{{ $titulo->id }}"
                                        data-nome="{{ $titulo->nome }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <form action="{{ route('excluir_titulo') }}" method="POST" onsubmit="return confirm('Excluir permanentemente?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $titulo->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-5 text-muted">Nenhum título cadastrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNovoTitulo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('salvar_titulo') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Novo Título</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarTitulo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('atualizar_titulo') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Editar Título</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Título</label>
                        <input type="text" name="nome" id="edit-nome" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalEditar = document.getElementById('modalEditarTitulo');
        if (modalEditar) {
            modalEditar.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                modalEditar.querySelector('#edit-id').value = button.getAttribute('data-id');
                modalEditar.querySelector('#edit-nome').value = button.getAttribute('data-nome');
                modalEditar.querySelector('#edit-descricao').value = button.getAttribute('data-descricao');
            });
        }
    });
</script>
@endsection