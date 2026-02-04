@extends('layouts.main_layout')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-header py-3">
            <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i>Controle de Acessos</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Nível Atual</th>
                        <th class="text-center">Alterar para</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $user)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ $user->name }}</span><br>
                            <small class="text-muted">{{ $user->email }}</small>
                        </td>
                        <td>
                            <span class="badge 
                                @if($user->tipo === 'admin') bg-danger 
                                    @elseif($user->tipo === 'organizador') bg-warning text-dark
                                         @else bg-primary
                                @endif">
                                {{ strtoupper($user->tipo) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('atualizar_tipo_usuario') }}" method="POST" class="d-flex gap-2 justify-content-center">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <select name="tipo" class="form-select form-select-sm" style="width: 150px">
                                    <option value="jogador" {{ $user->tipo == 'jogador' ? 'selected' : '' }}>Jogador</option>
                                    <option value="organizador" {{ $user->tipo == 'organizador' ? 'selected' : '' }}>Organizador</option>
                                    <option value="admin" {{ $user->tipo == 'admin' ? 'selected' : '' }}>Administrador</option>
                                </select>
                                <button class="btn btn-sm btn-dark"><i class="bi bi-save"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection