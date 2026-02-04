<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perfil - PiraVôlei</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        :root {
            --pira-sky-blue: #29ABE2;
            --pira-deep-blue: #005581;
            --pira-orange: #F7931E;
            --dark-bg: #0b1727;
            --dark-card: #162231;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #f8fafc;
            transition: background-color 0.3s ease;
            overflow-x: hidden; /* Garante que nada escape pela lateral */
        }

        [data-bs-theme="dark"] body { background-color: var(--dark-bg); color: #f1f5f9; }

        /* Card Elegante e Adaptativo */
        .glass-card {
            border-radius: 2rem;
            border: 1px solid rgba(0,0,0,0.05);
            background: white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
            width: 100%; /* Ocupa a largura do container */
        }

        [data-bs-theme="dark"] .glass-card {
            background: var(--dark-card);
            border-color: rgba(255,255,255,0.05);
        }

        /* Inputs que não estouram a tela */
        .form-control-pira {
            border-radius: 0.8rem;
            padding: 0.7rem 1rem;
            border: 2px solid #f1f5f9;
            background-color: #f8fafc;
            width: 100%;
        }

        [data-bs-theme="dark"] .form-control-pira {
            background-color: #0b1727;
            border-color: #232e3c;
            color: white;
        }

        .btn-pira {
            background-color: var(--pira-orange);
            color: white;
            border: none;
            font-weight: 700;
            border-radius: 1rem;
            padding: 0.7rem 1.5rem;
        }

        /* Container que limita a largura sem causar scroll */
        .profile-container {
            width: 100%;
            max-width: 920px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body class="py-4 py-md-5">
    <div class="container profile-container">
        
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Meu <span style="color: var(--pira-orange);">Perfil</span></h3>
                <p class="text-secondary small d-none d-sm-block">Gerencie suas informações</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Inicio</a>
        </header>

        <div class="glass-card p-4 p-md-5">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="glass-card p-4 p-md-5">
            @include('profile.partials.update-password-form')
        </div>

        <div class="glass-card p-4 p-md-5 border-start border-danger border-4">
            @include('profile.partials.delete-user-form')
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Detector de tema do sistema
        const applyTheme = () => {
            const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
        }
        applyTheme();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);
    </script>
</body>
</html>