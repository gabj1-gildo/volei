@extends('layouts.main_layout')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm border-0">
        {{-- Cabeçalho padronizado --}}
        <div class="card-header bg-dark text-white py-3">
            <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i>Controle de Acessos</h5>
        </div>

        {{-- Exibição de Erros e Sucesso --}}
        @if(session('success'))
            <div class="alert alert-success m-3 border-0">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger m-3 border-0">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger m-3 border-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light text-muted small">
                    <tr>
                        <th class="ps-4">NOME / EMAIL</th>
                        <th>NÍVEL ATUAL</th>
                        <th class="text-center">ALTERAR PARA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $user)
                    <tr>
                        <td class="ps-4">
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
                            {{-- Verifica se o usuário da linha é admin --}}
                            @php $isTargetAdmin = ($user->tipo === 'admin'); @endphp

                            <form action="{{ route('atualizar_tipo_usuario') }}" method="POST" class="d-flex gap-2 justify-content-center">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                
                                <select name="tipo" class="form-select form-select-sm shadow-sm" 
                                        style="width: 150px" 
                                        {{ $isTargetAdmin ? 'disabled' : '' }}>
                                    <option value="jogador" {{ $user->tipo == 'jogador' ? 'selected' : '' }}>Jogador</option>
                                    <option value="organizador" {{ $user->tipo == 'organizador' ? 'selected' : '' }}>Organizador</option>
                                    <option value="admin" {{ $user->tipo == 'admin' ? 'selected' : '' }}>Administrador</option>
                                </select>

                                <button class="btn btn-sm btn-dark shadow-sm" 
                                        {{ $isTargetAdmin ? 'disabled' : '' }} 
                                        title="{{ $isTargetAdmin ? 'Contas de administrador são protegidas' : 'Salvar alteração' }}">
                                    <i class="bi {{ $isTargetAdmin ? 'bi-shield-lock' : 'bi-check-lg' }}"></i>
                                </button>
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