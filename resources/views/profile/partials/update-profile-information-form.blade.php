<section class="card border-0 shadow-sm mb-4" style="border-radius: 1.5rem;">
    <div class="card-body p-4 p-md-5">
        <header class="mb-4">
            <h4 class="fw-bold" style="color: var(--pira-deep-blue);">
                <span class="fs-4 me-2">🏐</span> Informações Pessoais
            </h4>
            <p class="text-secondary small fw-medium">Mantenha seus dados e e-mail atualizados para não perder nenhuma notificação das partidas.</p>
        </header>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf 
            @method('patch')
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-secondary-emphasis">NOME COMPLETO</label>
                    <input name="name" type="text" class="form-control form-control-lg bg-light border-0 rounded-4 px-4" value="{{ auth()->user()->name }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-secondary-emphasis">NOME DE USUÁRIO</label>
                    <input name="username" type="text" class="form-control form-control-lg bg-light border-0 rounded-4 px-4" value="{{ auth()->user()->username }}" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold small text-secondary-emphasis">E-MAIL</label>
                    <input name="email" type="email" class="form-control form-control-lg bg-light border-0 rounded-4 px-4" value="{{ auth()->user()->email }}" required>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-pira">
                    Salvar Alterações
                </button>
                
                @if (session('status') === 'profile-updated')
                    <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-orange-pira small fw-bold">
                        Dados atualizados com sucesso!
                    </span>
                @endif
            </div>
        </form>
    </div>
</section>