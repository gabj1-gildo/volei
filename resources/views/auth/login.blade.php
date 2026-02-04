<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PiraVôlei - Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --pira-sky-blue: #29ABE2;
            --pira-deep-blue: #005581;
            --pira-orange: #F7931E;
            --pira-sand: #FBB03B;
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
        }

        .bg-pira-identity {
            background: linear-gradient(180deg, var(--pira-sky-blue) 60%, var(--pira-deep-blue) 60%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

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
            padding: 1rem 2rem;
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

        .volleyball-icon {
            font-size: 6rem;
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));
            animation: float 3s ease-in-out infinite;
            display: inline-block;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .form-control-pira {
            border-radius: 1rem;
            padding: 0.75rem 1.2rem;
            border: 2px solid #eee;
            transition: all 0.3s ease;
        }

        [data-bs-theme="dark"] .form-control-pira {
            background-color: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.1);
            color: white;
        }

        .form-control-pira:focus {
            border-color: var(--pira-sky-blue);
            box-shadow: 0 0 0 0.25 margin-top: rgba(41, 171, 226, 0.25);
        }
    </style>
</head>
<body class="d-flex flex-column align-items-center justify-content-center p-4">
    
    <header class="w-100 mb-5" style="max-width: 1000px;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('img/logo_piravolei.png') }}" style="width: 50px; border-radius: 10px;" alt="PiraVôlei">
                <h2 class="fw-bold mb-0" style="color: var(--pira-deep-blue);">PiraVôlei</h2>
            </div>
            <nav>
                <a href="{{ url('/') }}" class="text-decoration-none fw-bold text-secondary">Início</a>
            </nav>
        </div>
    </header>

    <main class="card hero-card w-100 border-0" style="max-width: 1000px;">
        <div class="row g-0">
            <div class="col-lg-7 p-4 bg-body d-flex flex-column justify-content-center">
                <h1 class="display-5 fw-black mb-2" style="line-height: 1.1;">Bem-vindo ao <br><span class="text-orange-pira">PiraVôlei</span></h1>
                <p class="text-secondary mb-4">Entre com seus dados para se inscrever em uma partida.</p>

                <x-auth-session-status class="mb-4 text-success fw-bold" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-uppercase text-secondary">E-mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" 
                               class="form-control form-control-pira @error('email') is-invalid @enderror" 
                               required autofocus autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="password" class="form-label small fw-bold text-uppercase text-secondary">Senha</label>
                            @if (Route::has('password.request'))
                                <a class="small text-decoration-none text-orange-pira fw-bold" href="{{ route('password.request') }}">
                                    Esqueceu?
                                </a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" 
                               class="form-control form-control-pira @error('password') is-invalid @enderror" 
                               required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                        <label for="remember_me" class="form-check-label small text-secondary">Manter conectado</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-pira">
                            Entrar 
                            {{-- na Quadra 🏐 --}}
                        </button>
                    </div>

                    @if (Route::has('register'))
                        <p class="text-center mt-4 small text-secondary">
                            Ainda não faz parte? <a href="{{ route('register') }}" class="fw-bold text-decoration-none" style="color: var(--pira-deep-blue);">Cadastre-se agora</a>
                        </p>
                    @endif
                </form>
            </div>

            <div class="col-lg-5 bg-pira-identity p-5 d-none d-lg-flex">
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