<section class="card border-0 shadow-sm mb-4" style="border-radius: 1.5rem;">
    <div class="card-body p-4 p-md-5">
        <header class="mb-4">
            <h4 class="fw-bold" style="color: var(--pira-deep-blue);">
                <span class="fs-4 me-2">🔒</span> Segurança da Conta
            </h4>
            <p class="text-secondary small fw-medium">Atualize sua senha para manter sua conta protegida e continuar marcando seus pontos.</p>
        </header>

        <form method="post" action="{{ route('password.update') }}">
            @csrf 
            @method('put')

            <div class="row g-4 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-bold small text-secondary-emphasis">SENHA ATUAL</label>
                    <input name="current_password" type="password" class="form-control form-control-pira form-control-lg bg-light border-0 rounded-4 px-4 @error('current_password', 'updatePassword') is-invalid @enderror" placeholder="Sua senha atual">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-secondary-emphasis">NOVA SENHA</label>
                    <input name="password" type="password" class="form-control form-control-pira form-control-lg bg-light border-0 rounded-4 px-4 @error('password', 'updatePassword') is-invalid @enderror" placeholder="Sua nova senha">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-secondary-emphasis">CONFIRMAR NOVA SENHA</label>
                    <input name="password_confirmation" type="password" class="form-control form-control-pira form-control-lg bg-light border-0 rounded-4 px-4 @error('password_confirmation', 'updatePassword') is-invalid @enderror" placeholder="Repita a nova senha">
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-pira" style="background-color: var(--pira-deep-blue); box-shadow: 0 10px 20px rgba(0, 85, 129, 0.2);">
                    Alterar Senha
                </button>
                
                @if (session('status') === 'password-updated')
                    <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-success small fw-bold">
                        Senha atualizada com segurança!
                    </span>
                @endif
            </div>
        </form>
    </div>
</section>