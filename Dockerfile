FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev zip curl \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader \
    && php vendor/bin/rr get-binary

EXPOSE 10000

# 🔥 ISSO É O QUE ESTAVA FALTANDO
USER www-data

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=10000