<!DOCTYPE html>
{{-- seguir tema do dispositivo --}}
<html lang="pt-br" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vôlei App - Gestão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        .footer { margin-top: auto; }
        .card { border-radius: 12px; }
    </style>

    
    <script>
        /*!
        * Script para detectar preferência de tema do sistema
        */
        (() => {
            'use strict'

            const getStoredTheme = () => localStorage.getItem('theme')
            const getPreferredTheme = () => {
                const storedTheme = getStoredTheme()
                if (storedTheme) {
                    return storedTheme
                }
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
            }

            const setTheme = theme => {
                if (theme === 'auto') {
                    document.documentElement.setAttribute('data-bs-theme', (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'))
                } else {
                    document.documentElement.setAttribute('data-bs-theme', theme)
                }
            }

            setTheme(getPreferredTheme())

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                const storedTheme = getStoredTheme()
                if (storedTheme !== 'light' && storedTheme !== 'dark') {
                    setTheme(getPreferredTheme())
                }
            })
        })()
    </script>

</head>
<body clas="d-flex flex-column  min-vh-100">
    @include('top_bar')

    <main class="flex-fill">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <footer class="footer py-4 border-top bg-body-tertiary">
        <div class="container text-center">
            <div class="row align-items-center">
                <div class="col-md-4 text-md-start mb-3 mb-md-0">
                    <span class="text-body-secondary small">&copy; 2026 Sistema de Vôlei</span>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <p class="mb-0 fw-bold">Desenvolvido por <span class="text-primary">Gildo Alves Batista Júnior</span></p>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>