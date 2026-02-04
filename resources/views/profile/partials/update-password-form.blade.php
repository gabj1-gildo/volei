<section>
    <header class="mb-4">
        <h4 class="fw-bold text-dark">{{ __('Segurança da Conta') }}</h4>
        <p class="text-secondary small text-uppercase">Atualize sua senha para manter sua conta protegida.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label class="small fw-bold text-secondary">SENHA ATUAL</label>
            <input name="current_password" type="password" class="form-control form-control-pira @error('current_password', 'updatePassword') is-invalid @enderror">
        </div>

        <div class="mb-3">
            <label class="small fw-bold text-secondary">NOVA SENHA</label>
            <input name="password" type="password" class="form-control form-control-pira @error('password', 'updatePassword') is-invalid @enderror">
        </div>

        <div class="mb-4">
            <label class="small fw-bold text-secondary">CONFIRMAR NOVA SENHA</label>
            <input name="password_confirmation" type="password" class="form-control form-control-pira @error('password_confirmation', 'updatePassword') is-invalid @enderror">
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-pira shadow-sm">Alterar Senha</button>
            @if (session('status') === 'password-updated')
                <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-success small fw-bold">Senha atualizada!</span>
            @endif
        </div>
    </form>
</section>