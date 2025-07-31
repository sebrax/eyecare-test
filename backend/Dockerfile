FROM php:8.2-cli-alpine

# ----
# 1 - Instalar dependências do sistema e extensões PHP necessárias para o Laravel
# ----
RUN apk add --no-cache \
    bash mysql-client oniguruma-dev libxml2-dev zip unzip curl icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring xml intl zip gd bcmath

# ----
# 2 - Instalar Composer copiando o binário da imagem oficial do Composer
# ----
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ----
# 3 - Definir diretório de trabalho dentro do container
# ----
WORKDIR /var/www/html

# ----
# 4 - Copiar todo o código da aplicação para dentro do container
# ----
COPY . .

# ----
# 5 - Copiar arquivo .env.example para .env para configuração padrão
# ----
RUN cp .env.example .env

# ----
# 6 - Instalar dependências PHP do projeto e otimizar autoload
# ----
RUN composer install --optimize-autoloader

# ----
# 7 - Ajustar permissões das pastas storage e bootstrap/cache para o usuário www-data
# ----
RUN chown -R www-data:www-data storage bootstrap/cache

# ----
# 8 - Expor porta 9000 para o servidor Laravel
# ----
EXPOSE 9000

# ----
# 9 - Comando padrão ao iniciar container: aguarda banco, executa migrate e seed, depois roda Laravel no built-in server
# ----
CMD ["sh", "-c", "composer install --optimize-autoloader; echo 'Aguardando banco...'; while ! nc -z $DB_HOST 3306; do sleep 2; done; php artisan migrate --force; php artisan db:seed --force; php artisan serve --host=0.0.0.0 --port=9000"]
