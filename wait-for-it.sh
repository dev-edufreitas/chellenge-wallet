#!/bin/bash
# Script para corrigir problemas de conexão Laravel-MySQL no Docker

# 1. Corrigir o Dockerfile
echo "Atualizando Dockerfile..."
cat > Dockerfile << 'EOF'
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install zip pdo_mysql bcmath

WORKDIR /var/www/html

COPY . .
RUN chown -R www-data:www-data /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expor a porta para o servidor artisan
EXPOSE 8000

# Esperar pelo MySQL antes de iniciar
COPY wait-for-db.sh /usr/local/bin/wait-for-db.sh
RUN chmod +x /usr/local/bin/wait-for-db.sh
EOF

# 2. Criar script wait-for-db.sh para garantir que o MySQL esteja pronto
echo "Criando script wait-for-db.sh..."
cat > wait-for-db.sh << 'EOF'
#!/bin/bash
# Script para aguardar a inicialização do MySQL

set -e

host="$1"
shift
cmd="$@"

until php -r "try {
    \$pdo = new PDO('mysql:host=$host;dbname=wallet', 'walletuser', 'walletpassword');
    echo 'MySQL conectado com sucesso!';
    exit(0);
} catch (PDOException \$e) {
    echo 'MySQL indisponível - ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}"; do
  echo "MySQL ainda não está disponível, aguardando..."
  sleep 2
done

echo "MySQL está pronto!"
exec $cmd
EOF

chmod +x wait-for-db.sh

# 3. Atualizar o docker-compose.yml
echo "Atualizando docker-compose.yml..."
cat > docker-compose.yml << 'EOF'
services:
  app:
    build: .
    container_name: wallet-app
    restart: unless-stopped
    ports:
      - "8000:8000"
    volumes:
      - ./:/var/www/html
    command: sh -c "/usr/local/bin/wait-for-db.sh db && php artisan migrate && php artisan serve --host=0.0.0.0 --port=8000"
    depends_on:
      - db
    networks:
      - wallet-network

  db:
    image: mysql:8.3
    container_name: wallet-db
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: wallet
      MYSQL_USER: walletuser
      MYSQL_PASSWORD: walletpassword
    ports:
      - "3306:3306"
    volumes:
      - wallet-db-data:/var/lib/mysql
    networks:
      - wallet-network
    command: --default-authentication-plugin=mysql_native_password

networks:
  wallet-network:
    driver: bridge

volumes:
  wallet-db-data:
EOF

# 4. Atualizar o arquivo .env
echo "Verificando .env..."
if grep -q "DB_HOST=db" .env; then
  echo "Configuração DB_HOST já está correta."
else
  echo "Atualizando DB_HOST para 'db'..."
  sed -i 's/DB_HOST=.*/DB_HOST=db/' .env
fi

echo "Verificando outras configurações do banco de dados..."
sed -i 's/DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=wallet/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=walletuser/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=walletpassword/' .env

echo "Configuração concluída! Agora você pode executar:"
echo "docker-compose down -v" 
echo "docker-compose up --build"