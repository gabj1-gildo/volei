<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="auto">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>PiraVôlei - Pirapora</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <style>
            :root {
                /* Cores extraídas diretamente da Logo */
                --pira-sky-blue: #29ABE2;
                --pira-deep-blue: #005581;
                --pira-orange: #F7931E;
                --pira-sand: #FBB03B;
                
                /* Modo Dark Suave */
                --dark-bg: #001a29;
                --dark-card: #002d45;
                --dark-text: #e0f2fe;
            }

            body {
                font-family: 'Instrument Sans', sans-serif;
                background-color: #f0f4f8;
                transition: background-color 0.3s ease;
                min-height: 100vh;
            }

            /* Ajuste do Modo Dark */
            [data-bs-theme="dark"] body {
                background-color: var(--dark-bg);
                color: var(--dark-text);
            }

            .hero-card {
                border-radius: 3rem;
                overflow: hidden;
                border: none;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
                background-color: white;
            }

            [data-bs-theme="dark"] .hero-card {
                background-color: var(--dark-card) !important;
                box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            }

            /* Lado Direito - Estética da Logo */
            .bg-pira-identity {
                background: linear-gradient(180deg, var(--pira-sky-blue) 60%, var(--pira-deep-blue) 60%);
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            /* Faixa de areia da logo entre os azuis */
            .bg-pira-identity::after {
                content: "";
                position: absolute;
                width: 100%;
                height: 15px;
                background-color: var(--pira-sand);
                top: 60%;
                transform: translateY(-50%);
            }

            .btn-pira {
                background-color: var(--pira-orange);
                border: none;
                color: white;
                font-weight: 800;
                padding: 1.2rem 2rem;
                border-radius: 1.5rem;
                text-transform: uppercase;
                letter-spacing: 1px;
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                box-shadow: 0 10px 20px rgba(247, 147, 30, 0.3);
            }

            .btn-pira:hover {
                background-color: #d67d16;
                transform: scale(1.05) translateY(-5px);
                box-shadow: 0 15px 25px rgba(247, 147, 30, 0.4);
                color: white;
            }

            .text-orange-pira {
                color: var(--pira-orange);
                font-weight: 800;
            }

            .logo-header {
                width: 50px;
                height: auto;
                border-radius: 10px;
            }

            .volleyball-icon {
                font-size: 6rem;
                filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));
                animation: float 3s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-20px); }
            }

            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-20px); }
            }

            /* E isso aplica a animação na classe da bola */
            .volleyball-icon {
                font-size: 5rem;
                filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));
                animation: float 3s ease-in-out infinite;
                display: inline-block; /* Garante que o transform funcione bem */
            }
        </style>
    </head>
    <body class="d-flex flex-column align-items-center justify-content-center p-4">
        
        <header class="w-100 mb-5" style="max-width: 1000px;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('img/logo_piravolei.png') }}" class="logo-header" alt="PiraVôlei">
                    <h2 class="fw-bold mb-0" style="color: var(--pira-deep-blue);">PiraVôlei</h2>
                </div>
                <nav>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary fw-bold text-secondary me-3">Entrar</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4 fw-bold" style="background-color: var(--pira-deep-blue); border: none;">Cadastrar</a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </div>
        </header>

        <main class="card hero-card w-100 border-0" style="max-width: 1000px;">
            <div class="row g-0">
                <div class="col-lg-7 p-5 bg-body d-flex flex-column justify-content-center">
                    <h1 class="display-3 fw-black mb-3" style="line-height: 1;">O vôlei em <br><span class="text-orange-pira">Pirapora</span></h1>
                    <p class="lead text-secondary mb-4 fs-4">A plataforma oficial de agendamento de partidas. Onde o esporte e a diversão se encontram.</p>
                    
                    <div class="row g-3 mb-5">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light dark:bg-opacity-10">
                                <span class="fs-2">🏐</span>
                                <div>
                                    <h6 class="mb-0 fw-bold text-secondary-emphasis">Partidas Reais</h6>
                                    <small class="text-muted text-secondary-emphasis fw-medium">Encontre o seu time.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light dark:bg-opacity-10">
                                <span class="fs-2">🔥</span>
                                <div>
                                    <h6 class="mb-0 fw-bold text-secondary-emphasis">Competição</h6>
                                    <small class="text-secondary-emphasis fw-medium">Partidas acirradas.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (Route::has('register'))
                        {{-- se usuario estiver logado --}}
                        @if (Auth::check())
                            <a href="{{ url('/dashboard') }}" class="btn btn-pira shadow me-3">Dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-pira shadow">Criar minha conta agora</a>
                        @endif
                    @endif
                </div>

                <div class="col-lg-5 bg-pira-identity p-5">
                    <div class="text-center z-3">
                        <div class="volleyball-icon">🏐</div>
                        <h2 class="text-white fw-black mt-4 display-6">PiraVôlei</h2>
                        <p class="text-white opacity-75 fw-medium tracking-wider text-uppercase">O esporte pega fogo!</p>
                        
                        <div class="mt-4 p-2 px-4 rounded-pill border border-white border-opacity-25 d-inline-block">
                             <small class="text-white fw-bold">📍 Pirapora - MG</small>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="mt-5 text-secondary">
            <small>© {{ date('Y') }} PiraVôlei - Desenvolvido para a comunidade de Pirapora</small>
        </footer>

        <script>
            const handleTheme = () => {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                } else {
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                }
            }
            handleTheme();
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', handleTheme);
        </script>
    </body>
</html>