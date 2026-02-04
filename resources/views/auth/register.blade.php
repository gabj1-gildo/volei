<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PiraVôlei - Criar Conta</title>

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
            min-height: 100vh;
            overflow-x: hidden;
        }

        [data-bs-theme="dark"] body { background-color: var(--dark-bg); color: var(--dark-text); }

        .hero-card {
            max-width: 920px;
            width: 100%;
            border-radius: 3rem;
            overflow: hidden;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
            background-color: white;
            margin: 0 auto;
        }

        [data-bs-theme="dark"] .hero-card { background-color: var(--dark-card) !important; }

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
            padding: 0.8rem 1.5rem;
            border-radius: 1.2rem;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .btn-pira:hover {
            background-color: #d67d16;
            transform: scale(1.03);
            color: white;
        }

        .volleyball-icon {
            font-size: 5rem;
            animation: float 3s ease-in-out infinite;
            display: inline-block;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .form-control-pira {
            border-radius: 0.8rem;
            padding: 0.6rem 1rem;
            border: 2px solid #f1f1f1;
            width: 100%;
        }

        [data-bs-theme="dark"] .form-control-pira {
            background-color: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.1);
            color: white;
        }
    </style>
</head>
<body class="d-flex flex-column align-items-center justify-content-center p-3">
    
    <header class="w-100 mb-4" style="max-width: 920px;">
        <div class="d-flex justify-content-between align-items-center px-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('/') }}"><img src="{{ asset('img/logo_piravolei.png') }}" style="width: 40px;" alt="PiraVôlei">
                </a>
                <h4 class="fw-bold mb-0" style="color: var(--pira-deep-blue);">PiraVôlei</h4>
            </div>
            <a href="{{ url("/") }}" class="btn btn-outline-info fw-bold me-3">← Início</a>
        </div>
    </header>

    <main class="card hero-card">
        <div class="row g-0">
            <div class="col-lg-7 p-4 p-md-5 bg-body d-flex flex-column justify-content-center">
                <h2 class="fw-bold mb-1">Criar <span style="color: var(--pira-orange);">Conta</span></h2>
                <p class="text-secondary small mb-4">Junte-se à maior comunidade de vôlei de Pirapora.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary text-uppercase">Nome</label>
                            <input type="text" name="name" class="form-control form-control-pira @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary text-uppercase">Username</label>
                            <input type="text" name="username" class="form-control form-control-pira @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="small fw-bold text-secondary text-uppercase">E-mail</label>
                            <input type="email" name="email" class="form-control form-control-pira @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary text-uppercase">Senha</label>
                            <input type="password" name="password" class="form-control form-control-pira @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary text-uppercase">Confirmar Senha</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-pira" required>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-pira shadow-sm">Cadastrar Agora</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <span class="small text-muted">Já tem uma conta?</span> 
                    <a href="{{ route('login') }}" class="small fw-bold text-decoration-none" style="color: var(--pira-deep-blue);">Faça Login</a>
                </div>
            </div>

            <div class="col-lg-5 bg-pira-identity p-5 d-none d-lg-flex text-center">
                <div class="z-3">
                    <div class="volleyball-icon">🏐</div>
                    <h3 class="text-white fw-bold mt-3">PiraVôlei</h3>
                    <p class="text-white opacity-75 small">Onde o esporte pega fogo!</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="mt-4 text-secondary">
        <small>© {{ date('Y') }} PiraVôlei</small>
    </footer>

    <script>
        const handleTheme = () => {
            const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
        }
        handleTheme();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', handleTheme);
    </script>
</body>
</html>