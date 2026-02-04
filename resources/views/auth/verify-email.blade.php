<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PiraVôlei - Confirme seu Email</title>

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

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #e3f2fd 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        [data-bs-theme="dark"] body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #003a65 100%);
        }

        .hero-card {
            border-radius: 1.5rem;
            overflow: hidden;
            border: none;
            box-shadow: 0 15px 35px -10px rgba(0,0,0,0.15);
            background: white;
            width: 100%;
            max-width: 400px;
            max-height: 95vh;
            margin: 0 auto;
        }

        [data-bs-theme="dark"] .hero-card {
            background: var(--dark-card) !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        /* Mobile First - Stack vertical em mobile */
        .row.content-row {
            flex-direction: column-reverse;
        }

        @media (min-width: 576px) {
            .row.content-row {
                flex-direction: row;
            }
            .col-md-8 { flex: 0 0 66.66667%; max-width: 66.66667%; }
            .col-md-4 { flex: 0 0 33.33333%; max-width: 33.33333%; }
        }

        .content-section {
            padding: 2rem 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 350px;
        }

        .bg-pira-identity {
            background: linear-gradient(180deg, var(--pira-sky-blue) 45%, var(--pira-deep-blue) 55%);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 0.75rem;
            min-height: 180px;
        }

        .bg-pira-identity::after {
            content: "";
            position: absolute;
            width: 100%;
            height: 5px;
            background: var(--pira-sand);
            top: 50%;
            transform: translateY(-50%);
        }

        .btn-pira, .btn-secondary-pira {
            padding: 0.85rem 1rem;
            border-radius: 0.875rem;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 0.75rem;
            border: none;
        }

        .btn-pira {
            background: var(--pira-orange);
            color: white;
            box-shadow: 0 6px 12px rgba(247,147,30,0.3);
        }

        .btn-pira:hover, .btn-pira:active {
            background: #d67d16;
            transform: translateY(-1px);
            box-shadow: 0 8px 15px rgba(247,147,30,0.4);
            color: white;
        }

        .btn-secondary-pira {
            border: 2px solid var(--pira-orange);
            color: var(--pira-orange);
            background: transparent;
        }

        .btn-secondary-pira:hover, .btn-secondary-pira:active {
            background: var(--pira-orange);
            color: white;
            transform: translateY(-1px);
        }

        .volleyball-icon {
            font-size: 3rem;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
            animation: float 2s ease-in-out infinite;
            margin-bottom: 0.5rem;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .email-icon {
            font-size: 3rem;
            color: var(--pira-sky-blue);
            background: rgba(41,171,226,0.15);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 1rem auto 0.75rem;
            border: 2px solid rgba(41,171,226,0.3);
        }

        .info-badge {
            background: linear-gradient(45deg, var(--pira-sky-blue), #29ABE2);
            color: white;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        h1 {
            font-size: clamp(1.4rem, 4vw, 1.6rem);
            line-height: 1.2;
            margin-bottom: 1rem;
            font-weight: 900;
        }

        .lead {
            font-size: clamp(0.9rem, 3vw, 1rem);
            margin-bottom: 1rem;
        }

        .email-highlight {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 0.4rem;
            font-weight: 600;
            color: var(--pira-deep-blue);
            font-size: 0.9rem;
        }

        .tip-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .tip-box strong {
            font-size: 0.9rem;
            display: block;
            margin-bottom: 0.25rem;
        }

        /* Safe area para notch */
        @supports(padding: max(0)) {
            body {
                padding-bottom: env(safe-area-inset-bottom, 0.5rem);
                padding-top: env(safe-area-inset-top, 0.5rem);
            }
        }

        /* Scroll suave se necessário */
        .hero-card {
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body>
    <div class="hero-card">
        <div class="row g-0 content-row">
            <!-- Conteúdo Principal -->
            <div class="col-12 col-md-8">
                <div class="content-section">
                    <div class="text-center">
                        <h1 style="color: var(--pira-deep-blue);">
                            <span style="color: var(--pira-orange);">PiraVôlei</span><br>
                            Confirme sua conta
                        </h1>
                        
                        <div class="email-icon">✉️</div>
                        <span class="info-badge">Verifique seu Email</span>
                        
                        <p class="lead text-secondary">
                            Enviamos um email para <strong class="email-highlight">{{ auth()->user()->email ?? 'seu@email.com' }}</strong>
                        </p>
                        <small class="text-muted d-block mb-3 text-center fw-medium">
                            Clique no link para ativar sua conta e agendar partidas!
                        </small>

                        <div class="tip-box">
                            <strong>🔍 Não recebeu?</strong>
                            <span class="text-muted d-block">Verifique spam ou clique abaixo.</span>
                        </div>

                        <form method="POST" action="{{ route('verification.send') }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-pira">
                                📧 Reenviar Email
                            </button>
                        </form>

                        <a href="{{ route('login') }}" class="btn btn-secondary-pira">
                            ✅ Já confirmei, entrar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Identidade Visual (sempre no topo em mobile) -->
            <div class="col-12 col-md-4 bg-pira-identity">
                <div class="text-center text-white" style="position: relative; z-index: 3;">
                    <div class="volleyball-icon">🏐</div>
                    <h4 class="fw-black mb-1" style="font-size: clamp(1.1rem, 4vw, 1.3rem);">PiraVôlei</h4>
                    <small class="opacity-75 fw-medium" style="font-size: 0.8rem;">Pirapora - MG</small>
                </div>
            </div>
        </div>
    </div>

    <script>
        const handleTheme = () => {
            document.documentElement.setAttribute('data-bs-theme', 
                window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
            );
        }
        handleTheme();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', handleTheme);
    </script>
</body>
</html>
