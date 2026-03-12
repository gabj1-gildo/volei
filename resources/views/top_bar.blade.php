<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        {{-- Logo e Brand --}}
        <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ route('dashboard') }}">
            <img src="{{ asset('img/logo_piravolei.png') }}" alt="Logo PiraVôlei" width="32" height="32" class="me-2 rounded">
            <span class="text-primary">Pira</span>Vôlei
        </a>
        
        {{-- Botão Mobile --}}
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            {{-- Estilo para o hover dos links --}}
            <style>
                .navbar-nav .nav-item .nav-link {
                    border-radius: 6px;
                    padding: 0.5rem 0.8rem;
                    transition: all 0.2s ease-in-out;
                }
                .navbar-nav .nav-item .nav-link:hover {
                    background-color: rgba(255, 255, 255, 0.1);
                    color: #fff !important;
                }
                /* Garante que o item ativo (amarelo) mantenha a cor no hover */
                .navbar-nav .nav-item .nav-link.text-warning:hover {
                    color: #ffc107 !important; 
                }
            </style>

            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1 ms-lg-3">
                {{-- Visível para todos os logados --}} 
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'text-warning fw-bold' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-play-circle me-1"></i> Jogos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('minhas_inscricoes') ? 'text-warning fw-bold' : '' }}" href="{{ route('minhas_inscricoes') }}">
                        <i class="bi bi bi-journal-check me-1"></i> Minhas Inscrições
                    </a>
                </li>

                @if (Auth::user()->tipo == 'admin')
                    {{-- Visível para Admin (Gestão de Locais e Títulos) --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gerenciar_locais') ? 'text-warning fw-bold' : '' }}" href="{{ route('gerenciar_locais') }}">
                            <i class="bi bi-geo-alt me-1"></i> Locais
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gerenciar_titulos') ? 'text-warning fw-bold' : '' }}" href="{{ route('gerenciar_titulos') }}">
                            <i class="bi bi-tag me-1"></i> Títulos
                        </a>
                    </li>
                @endif
                
                {{-- Visível para Admin e Organizador (Logística) --}}
                @if(Auth::user()->tipo == 'admin' || Auth::user()->tipo == 'organizador')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gerenciar_jogos') ? 'text-warning fw-bold' : '' }}" href="{{ route('gerenciar_jogos') }}">
                            <i class="bi bi-calendar-event me-1"></i> Gerenciar Jogos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gerenciar_inscricoes') ? 'text-warning fw-bold' : '' }}" href="{{ route('gerenciar_inscricoes') }}">
                            <i class="bi bi-card-checklist me-1"></i> Inscrições
                        </a>
                    </li>
                @endif

                {{-- Exclusivo para Admin (Gestão de Pessoas) --}}
                @if(Auth::user()->tipo == 'admin')
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link {{ request()->routeIs('gerenciar_usuarios') ? 'text-warning fw-bold' : '' }}" href="{{ route('gerenciar_usuarios') }}">
                            <i class="bi bi-people-fill me-1"></i> Usuários
                        </a>
                    </li>
                @endif
            </ul>

            {{-- Área do Usuário (Direita) --}}
            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                <span class="text-light small d-none d-lg-block">
                    <i class="bi bi-person-circle me-1 text-secondary-bold"></i> Olá, <strong>{{ explode(' ', Auth::user()->name)[0] }}</strong>
                </span>
                
                {{-- Botões de Ação --}}
                <div class="d-flex gap-2">
                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-light 4nav-link {{ request()->routeIs('profile.edit') ? 'text-warning fw-bold' : '' }}" title="Meu Perfil">
                        <i class="bi bi-gear-fill"></i>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center gap-1" title="Sair do Sistema">
                            <i class="bi bi-box-arrow-right d-lg-none"></i> Sair
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</nav>