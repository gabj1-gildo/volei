FROM php:8.4-cli

# Instalar dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev zip curl \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Permissões de pasta
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Instalar dependências e baixar o binário do RoadRunner (Linux)
RUN composer install --no-dev --optimize-autoloader && \
    php vendor/bin/rr get-binary

# Expor a porta do Render/Railway
EXPOSE 10000

# Cache e Start via Octane
CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=10000