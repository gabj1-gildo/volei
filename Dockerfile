FROM php:8.4-cli

# 1. Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev zip curl libaio1 \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets

# --- INÍCIO DA CONFIGURAÇÃO ORACLE ---

WORKDIR /opt/oracle

# 2. BAIXAR O CLIENTE ORACLE DIRETO DA INTERNET (Solução para o erro do GitHub)
# Usamos o 'curl' para pegar os arquivos zip direto do servidor da Oracle.
# Isso evita que você tenha que commitar arquivos de 100MB+ no seu git.
RUN curl -o instantclient-basic.zip https://download.oracle.com/otn_software/linux/instantclient/2112000/instantclient-basic-linux.x64-21.12.0.0.0dbru.zip \
    && curl -o instantclient-sdk.zip https://download.oracle.com/otn_software/linux/instantclient/2112000/instantclient-sdk-linux.x64-21.12.0.0.0dbru.zip \
    && unzip instantclient-basic.zip \
    && unzip instantclient-sdk.zip \
    && rm *.zip \
    && mv instantclient_* instantclient

# 3. Definir onde o Linux deve procurar as bibliotecas
ENV LD_LIBRARY_PATH=/opt/oracle/instantclient

# 4. Instalar a extensão OCI8 no PHP
RUN docker-php-ext-configure oci8 --with-oci8=instantclient,/opt/oracle/instantclient \
    && docker-php-ext-install oci8

# --- FIM DA CONFIGURAÇÃO ORACLE ---

WORKDIR /var/www

# Copiar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar arquivos do projeto (Agora sem os zips pesados, isso é rápido)
COPY . .

# Permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www

# Instalar dependências e baixar binário do RoadRunner
RUN composer install --no-dev --optimize-autoloader \
    && php vendor/bin/rr get-binary

EXPOSE 10000

USER www-data

# Define a variável TNS para buscar a wallet nos secrets do Render
ENV TNS_ADMIN=/etc/secrets

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=10000