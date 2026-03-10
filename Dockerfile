FROM php:8.4-cli

# 1. Dependências de sistema
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev zip curl ca-certificates \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 2. Copia arquivos de dependência
COPY composer.json composer.lock ./

# 3. Instalamos TUDO (incluindo scripts) em um único passo
# Isso evita o erro do RoadRunner reclamar que o vendor está incompleto
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 4. Agora sim, baixamos o binário do RoadRunner
# Se o comando acima funcionou, o vendor/bin/rr já está pronto
RUN php vendor/bin/rr get-binary

# 5. Copia o resto do código
COPY . .

# 6. Ajuste de permissões
RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

USER www-data

CMD php artisan migrate --force && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=10000