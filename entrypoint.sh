#!/bin/sh

# 1. Preparação (Otimização e Banco)
php artisan migrate --force
php artisan route:cache
php artisan view:cache
php artisan config:cache

# 2. Inicia o Scheduler em Loop (Background)
# Ele roda a cada 60 segundos para verificar o console.php
(while [ true ]; do
  php /var/www/artisan schedule:run --no-interaction
  sleep 60
done) &

# 3. Inicia o servidor principal (Foreground)
# O container permanece vivo enquanto este processo estiver rodando
php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=10000