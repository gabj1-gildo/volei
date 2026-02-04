# Imagem base
FROM php:8.2-cli

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    curl \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do projeto
COPY . .

# Permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Instalar dependências do Laravel (Somente o que é necessário para o build)
RUN composer install --no-dev --optimize-autoloader

# Expor porta usada pelo Render
EXPOSE 10000

# O SEGREDO: Comandos de runtime vão no CMD
CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=10000