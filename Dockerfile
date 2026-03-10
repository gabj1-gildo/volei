FROM php:8.4-cli

# 1. Instalando dependências e ca-certificates (essencial para downloads HTTPS no build)
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev zip curl ca-certificates \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 2. Copiando apenas os arquivos do composer primeiro (Otimiza o cache do Docker)
COPY composer.json composer.lock ./

# 3. Instalando dependências antes de copiar o resto do código
# Usamos --no-scripts para evitar que o Laravel tente rodar comandos antes de ter o código todo
RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .

# 4. Agora sim, baixamos o binário do RoadRunner como ROOT (para evitar erro 255 de permissão)
# E geramos o autoloader final
RUN php vendor/bin/rr get-binary && \
    composer install --no-dev --optimize-autoloader

# 5. Ajustando permissões (Importante: storage e bootstrap/cache precisam de escrita)
RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

# O usuário www-data só deve ser definido APÓS os comandos de instalação de sistema
USER www-data

# 6. Comando de inicialização
CMD php artisan migrate --force && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=10000