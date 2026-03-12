FROM php:8.4-cli

# Instalação de dependências
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev zip curl \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Ajuste de permissões para a pasta storage e bootstrap/cache
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

RUN composer install --no-dev --optimize-autoloader \
    && php vendor/bin/rr get-binary

# --- Configuração do Script de Entrada ---
USER root
# Copia o script para o sistema
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
# Dá permissão de execução
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 10000

# Voltamos para o usuário padrão do app por segurança
USER www-data

# O CMD agora chama o script que gerencia os dois processos
CMD ["/usr/local/bin/entrypoint.sh"]