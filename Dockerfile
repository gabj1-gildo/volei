FROM php:8.4-cli

# 1. Instalar dependências do sistema
# Mantemos o 'libaio1t64' e o 'ln -s' pois sua imagem base exige a versão nova (t64)
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev zip curl libaio1t64 \
    && ln -s /usr/lib/x86_64-linux-gnu/libaio.so.1t64 /usr/lib/x86_64-linux-gnu/libaio.so.1 \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets

# --- CONFIGURAÇÃO ORACLE ---

WORKDIR /opt/oracle

# 2. Baixar Instant Client (Mantendo o método CURL que funcionou)
RUN curl -o instantclient-basic.zip https://download.oracle.com/otn_software/linux/instantclient/2112000/instantclient-basic-linux.x64-21.12.0.0.0dbru.zip \
    && curl -o instantclient-sdk.zip https://download.oracle.com/otn_software/linux/instantclient/2112000/instantclient-sdk-linux.x64-21.12.0.0.0dbru.zip \
    && unzip instantclient-basic.zip \
    && unzip instantclient-sdk.zip \
    && rm *.zip \
    && mv instantclient_* instantclient

ENV LD_LIBRARY_PATH=/opt/oracle/instantclient

# 3. Instalar OCI8 via PECL (AQUI ESTÁ A CORREÇÃO DO ERRO)
# O erro "usage: docker-php-ext-configure" ocorreu porque o fonte não existia.
# Usamos o PECL para baixar, compilar e instalar, apontando o caminho do cliente.
RUN echo 'instantclient,/opt/oracle/instantclient' | pecl install oci8 \
    && docker-php-ext-enable oci8

# --- FIM CONFIGURAÇÃO ORACLE ---

WORKDIR /var/www

# Copiar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar arquivos do projeto
COPY . .

# Permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www

# Instalar dependências e baixar binário do RoadRunner
RUN composer install --no-dev --optimize-autoloader \
    && php vendor/bin/rr get-binary

EXPOSE 10000

USER www-data

# Define variável de ambiente para a Wallet
ENV TNS_ADMIN=/etc/secrets

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=10000