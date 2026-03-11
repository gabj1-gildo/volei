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
                    <tr class="{{ !$titulo->ativo ? 'opacity-50 bg-light' : '' }}">
                        <td>
                            <span class="fw-bold {{ !$titulo->ativo ? 'text-decoration-line-through text-muted' : '' }}">
                                {{ $titulo->nome }}
                            </span>
                            @if(!$titulo->ativo)
                                <span class="badge bg-danger ms-2" style="font-size: 0.7rem;">INATIVO</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                {{-- Botão Editar --}}
                                <button class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarTitulo"
                                        data-id="{{ $titulo->id }}"
                                        data-nome="{{ $titulo->nome }}"
                                        {{ !$titulo->ativo ? 'disabled' : '' }}>
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                {{-- Botão Ativar/Desativar --}}
                                <form action="{{ route('alternar_status_titulo') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $titulo->id }}">
                                    @if($titulo->ativo)
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
            @if(session('success') || session('error'))
                const toastEl = document.getElementById('toastSucesso');
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            @endif
        });
    </script>
@endsection