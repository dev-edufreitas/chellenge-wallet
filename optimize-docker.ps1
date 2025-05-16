# Otimizar configurações do Docker no Windows
# Execute como administrador

# 1. Aumentar recursos do Docker Desktop
Write-Host "Atualizando recursos do Docker Desktop..." -ForegroundColor Green

# Caminho para o arquivo settings.json do Docker Desktop
$dockerConfigPath = "$env:USERPROFILE\AppData\Roaming\Docker\settings.json"

# Verificar se o arquivo existe
if (Test-Path $dockerConfigPath) {
    # Ler o arquivo de configuração
    $config = Get-Content -Path $dockerConfigPath | ConvertFrom-Json
    
    # Atualizar configurações de recursos
    $config.cpus = 4  # Usar 4 CPUs
    $config.memoryMiB = 8192  # Usar 8GB de RAM
    
    # Ativar o modo WSL2 (geralmente mais rápido)
    $config.wslEngineEnabled = $true
    
    # Salvar as alterações
    $config | ConvertTo-Json -Depth 10 | Set-Content -Path $dockerConfigPath
    
    Write-Host "Configurações do Docker Desktop atualizadas. Reinicie o Docker Desktop para aplicar." -ForegroundColor Yellow
} else {
    Write-Host "Arquivo de configuração do Docker Desktop não encontrado em: $dockerConfigPath" -ForegroundColor Red
}

# 2. Criar docker-compose otimizado
Write-Host "Criando docker-compose.yml otimizado..." -ForegroundColor Green

$dockerComposeContent = @"
services:
  app:
    build: .
    container_name: wallet-app
    restart: unless-stopped
    ports:
      - "8000:8000"
    volumes:
      - ./:/var/www/html:cached
      - /var/www/html/vendor
      - /var/www/html/node_modules
    command: sh -c "/usr/local/bin/wait-for-db.sh db && php artisan migrate && php artisan serve --host=0.0.0.0 --port=8000"
    depends_on:
      - db
    networks:
      - wallet-network
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G

  vite:
    image: node:21-alpine
    container_name: wallet-vite
    working_dir: /app
    volumes:
      - ./:/app:cached
      - /app/node_modules
    ports:
      - "5173:5173"
    command: sh -c "npm install && npm run dev"
    networks:
      - wallet-network
    deploy:
      resources:
        limits:
          cpus: '1'
          memory: 1G

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
      - ./mysql-config:/etc/mysql/conf.d
    networks:
      - wallet-network
    command: --default-authentication-plugin=mysql_native_password
    deploy:
      resources:
        limits:
          cpus: '1'
          memory: 1G

networks:
  wallet-network:
    driver: bridge

volumes:
  wallet-db-data:
"@

# Salvar o arquivo docker-compose.yml
$dockerComposeContent | Out-File -FilePath "docker-compose.yml" -Encoding utf8

# 3. Criar diretório e arquivo de configuração MySQL
Write-Host "Criando configuração MySQL otimizada..." -ForegroundColor Green

# Criar diretório se não existir
if (-not (Test-Path "mysql-config")) {
    New-Item -Path "mysql-config" -ItemType Directory
}

$mysqlConfig = @"
[mysqld]
innodb_buffer_pool_size = 128M
innodb_log_file_size = 32M
max_connections = 50
key_buffer_size = 32M
thread_cache_size = 8
query_cache_size = 32M
query_cache_limit = 1M
join_buffer_size = 1M
max_heap_table_size = 32M
tmp_table_size = 32M
"@

# Salvar o arquivo my.cnf
$mysqlConfig | Out-File -FilePath "mysql-config\my.cnf" -Encoding utf8

# 4. Otimizar .env para Laravel
Write-Host "Otimizando arquivo .env..." -ForegroundColor Green

$envContent = @"
APP_NAME=Wallet
APP_ENV=local
APP_KEY=base64:w8uZHZAZrs6GpQrlVts8nzGrRBy+ADJWfwK5NG0TRsQ=
APP_DEBUG=false
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=wallet
DB_USERNAME=walletuser
DB_PASSWORD=walletpassword

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=file
CACHE_PREFIX=wallet

VITE_APP_NAME="Wallet"
"@

# Salvar o arquivo .env
$envContent | Out-File -FilePath ".env" -Encoding utf8

Write-Host "`nOtimizações concluídas!" -ForegroundColor Green
Write-Host "Execute os seguintes comandos para aplicar as alterações:" -ForegroundColor Yellow
Write-Host "docker-compose down" -ForegroundColor Cyan
Write-Host "docker-compose up --build -d" -ForegroundColor Cyan
Write-Host "`nSe o Docker Desktop estiver em execução, reinicie-o para aplicar as novas configurações de recursos." -ForegroundColor Yellow