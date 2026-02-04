@extends('layouts.main_layout')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">Inscrições Recebidas</h2>

    @foreach($jogos as $jogo)
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 text-primary fw-bold">
                {{ $jogo->titulo->nome }} - <small class="text-muted">{{ date('d/m', strtotime($jogo->data_hora)) }}</small>
            </h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">JOGADOR</th>
                        <th>STATUS</th>
                        <th class="text-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jogo->inscricoes as $inscricao)
                    <tr>
                        <td class="ps-4"><strong>{{ $inscricao->user->name }}</strong></td>
                        <td>
                            <span class="badge {{ $inscricao->status == 'confirmado' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($inscricao->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('alterar_status_inscricao') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="id_inscricao" value="{{ $inscricao->id }}">
                                <button name="status" value="confirmado" class="btn btn-sm btn-success shadow-sm">Aprovar</button>
                                <button name="status" value="cancelada" class="btn btn-sm btn-outline-danger">Recusar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center py-3 text-muted">Nenhuma inscrição.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection