<section>
    <h5 class="fw-bold mb-4" style="color: var(--pira-deep-blue);">Informações Pessoais</h5>
    <form method="post" action="{{ route('profile.update') }}">
        @csrf @method('patch')
        <div class="row g-3"> <div class="col-12 col-md-6">
                <label class="small fw-bold text-secondary">NOME</label>
                <input name="name" type="text" class="form-control form-control-pira" value="{{ auth()->user()->name }}">
            </div>
            <div class="col-12 col-md-6">
                <label class="small fw-bold text-secondary">E-MAIL</label>
                <input name="email" type="email" class="form-control form-control-pira" value="{{ auth()->user()->email }}">
            </div>
            <div class="col-12 col-md-6">
                <label class="small fw-bold text-secondary">Nome de Usuario</label>
                <input name="username" type="text" class="form-control form-control-pira" value="{{ auth()->user()->username }}">
            </div>
            
        </div>
        <button type="submit" class="btn btn-pira mt-4">Salvar</button>
    </form>
</section>