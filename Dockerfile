# 1. Puxa a imagem oficial do RoadRunner apenas para extrair o executável
FROM ghcr.io/roadrunner-server/roadrunner:latest AS roadrunner

# 2. Inicia a sua imagem PHP original
FROM php:8.4-cli

# Instalação de dependências
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev zip curl \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# 3. Copia o binário pronto do RoadRunner direto para a raiz do seu projeto!
COPY --from=roadrunner /usr/bin/rr /var/www/rr

# Ajuste de permissões (Adicionei a permissão de execução para o arquivo rr)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && chmod +x /var/www/rr

# 4. Roda o composer SEM o comando get-binary
RUN composer install --no-dev --optimize-autoloader

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