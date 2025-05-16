FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install zip pdo_mysql bcmath

WORKDIR /var/www/html

# Copie apenas os arquivos necessários primeiro (sem node_modules)
COPY composer.* ./
COPY package.json package-lock.json* ./

# Instale as dependências do Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-scripts --no-autoloader

# Agora copie o resto dos arquivos
COPY . .

# Gere o autoloader otimizado
RUN composer dump-autoload --optimize

# Configure permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponha porta para o servidor artisan
EXPOSE 8000

# Esperar pelo MySQL antes de iniciar
COPY wait-for-db.sh /usr/local/bin/wait-for-db.sh
RUN chmod +x /usr/local/bin/wait-for-db.sh