<section>
    <header class="mb-4 text-danger">
        <h4 class="fw-bold">{{ __('Excluir Conta') }}</h4>
        <p class="small opacity-75">Uma vez que sua conta for excluída, todos os dados de partidas e histórico serão removidos permanentemente.</p>
    </header>

    <button type="button" class="btn btn-outline-danger px-4 rounded-pill fw-bold" 
            data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
        Excluir minha conta
    </button>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4" style="border-radius: 2rem;">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <h5 class="fw-bold mb-3">Tem certeza absoluta?</h5>
                    <p class="small text-secondary mb-4">Para confirmar, digite sua senha atual abaixo.</p>
                    
                    <input name="password" type="password" class="form-control form-control-pira mb-3" placeholder="Sua senha">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Excluir para sempre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>