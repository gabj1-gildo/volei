<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PiraVôlei - Recuperar Senha</title>

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
        }

        [data-bs-theme="dark"] body { background-color: var(--dark-bg); color: var(--dark-text); }

        .hero-card {
            max-width: 920px;
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
        }
    </style>
</head>
<body class="d-flex flex-column align-items-center justify-content-center p-3">
    
    <header class="w-100 mb-4" style="max-width: 920px;">
        <div class="d-flex justify-content-between align-items-center px-2">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('img/logo_piravolei.png') }}" style="width: 40px;" alt="PiraVôlei">
                <h4 class="fw-bold mb-0" style="color: var(--pira-deep-blue);">PiraVôlei</h4>
            </div>
            <a href="{{ route('login') }}" class="text-secondary fw-bold text-decoration-none">← Voltar ao login</a>
        </div>
    </header>

    <main class="card hero-card w-100">
        <div class="row g-0">
            <div class="col-lg-7 p-4 p-md-5 bg-body d-flex flex-column justify-content-center">
                <h2 class="fw-bold mb-3">Recuperar <span style="color: var(--pira-orange);">Senha</span></h2>
                
                <div class="mb-4 text-secondary small">
                    {{ __('Esqueceu sua senha? Sem problemas. Basta nos informar seu endereço de e-mail e enviaremos um link para você escolher uma nova.') }}
                </div>

                <x-auth-session-status class="mb-4 text-success fw-bold small" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="small fw-bold text-secondary">E-MAIL CADASTRADO</label>
                        <input id="email" type="email" name="email" 
                               class="form-control form-control-pira @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" required autofocus placeholder="seu@email.com">
                        @error('email')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-pira shadow-sm">
                            {{ __('Enviar Link de Recuperação') }}
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted mb-0">Lembrou a senha? <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: var(--pira-deep-blue);">Entrar</a></p>
                </div>
            </div>

            <div class="col-lg-5 bg-pira-identity p-5 d-none d-lg-flex text-center">
                <div class="z-3">
                    <div class="volleyball-icon">🏐</div>
                    <h3 class="text-white fw-bold mt-4">PiraVôlei</h3>
                    <p class="text-white opacity-85 small">Sua quadra virtual em Pirapora.</p>
                    
                    <div class="mt-4 p-2 px-3 rounded-pill border border-white border-opacity-25 d-inline-block">
                        <small class="text-white fw-bold">Segurança PiraVôlei</small>
                    </div>
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