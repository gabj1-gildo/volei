<nav class="navbar navbar-expand-lg navbar shadow-sm">
    <div class="container">
        {{-- rota inicial --}}
        <a href="{{url('/')}}" class="nav-link"><img src="{{ asset('img/logo_piravolei.png') }}" alt="Logo PiraVôlei" width="30" height="30" class="me-2"></a>
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">PiraVôlei</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">

            <style>
        .navbar-nav .nav-item {
            transition: all 0.3s;
        }
        .navbar-nav .nav-item:hover {
            background-color: rgba(139, 139, 152, 0.627);
            border-radius: 5px;
        }
    </style>

            <ul class="navbar-nav me-auto">
                {{-- Visível para todos os logados --}} 
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">Jogos</a>
                </li>

                @if (Auth::user()->tipo=='admin')
                    {{-- Visível para Admin (Gestão de Locais e Títulos) --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('gerenciar_locais') }}">Gerenciar Locais</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('gerenciar_titulos') }}">Gerenciar Títulos</a>
                    </li>
    
                @endif
                {{-- Visível para Admin e Organizador (Logística) --}}
                @if(Auth::user()->tipo == 'admin' || Auth::user()->tipo == 'organizador')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('gerenciar_jogos') }}">Gerenciar Jogos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('gerenciar_inscricoes') }}">Inscrições</a>
                    </li>
                @endif

                {{-- Exclusivo para Admin (Gestão de Pessoas) --}}
                @if(Auth::user()->tipo == 'admin')
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="{{ route('gerenciar_usuarios') }}">
                            <i class="bi bi-people-fill"></i> Usuários
                        </a>
                    </li>
                @endif
            </ul>

            <div class="navbar-nav align-items-center">
                <span class="nav-link text-light me-3 small">Olá, {{ Auth::user()->name }}</span>
                
                {{-- Perfil --}}
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-info fw- btn-sm me-2">
                    <i class="bi bi-person"></i>
                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">Sair</button>
                </form>
            </div>
        </div>
    </div>
</nav>