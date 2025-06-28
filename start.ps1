# Inicia o script com erro se algum comando falhar
$ErrorActionPreference = "Stop"

Write-Host "🚀 Iniciando Sistema de Biblioteca..."

Write-Host "🧹 Parando e removendo containers e volumes antigos..."
docker-compose down -v

if (-Not (Test-Path -Path ".env")) {
    Write-Host "📋 Copiando arquivo .env..."
    Copy-Item -Path ".env.example" -Destination ".env"
}

Write-Host "🐳 Construindo containers Docker..."
docker-compose build --no-cache

Write-Host "🐳 Iniciando containers..."
docker-compose up -d

Write-Host "⏳ Aguardando MySQL ficar disponível..."
do {
    Start-Sleep -Seconds 2
    $mysqlReady = docker-compose exec db mysqladmin ping -h "127.0.0.1" --silent 2>&1
} while ($LASTEXITCODE -ne 0)

Write-Host "⏳ Aguardando container app ficar disponível..."
do {
    Start-Sleep -Seconds 2
    docker-compose exec app php -v > $null 2>&1
} while ($LASTEXITCODE -ne 0)

Write-Host "⚙️ Criando pastas necessárias..."
docker-compose exec app mkdir -p storage/framework/views,storage/framework/cache,storage/framework/sessions

Write-Host "⚙️ Ajustando permissões..."
# Atenção: chown e chmod são comandos Linux, não existem nativamente no Windows.
# Se o container for Linux, esses comandos funcionam dentro dele.
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache storage/framework
docker-compose exec app chmod -R 775 storage bootstrap/cache storage/framework

Write-Host "📦 Instalando dependências do Laravel..."
docker-compose exec app composer install --no-dev --optimize-autoloader

Write-Host "♻️ Limpando caches do Laravel..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
# docker-compose exec app php artisan view:clear

Write-Host "🔑 Gerando chave da aplicação..."
docker-compose exec app php artisan key:generate

Write-Host "🗄️ Executando migrations..."
docker-compose exec app php artisan migrate --force

Write-Host "🌱 Executando seeders..."
docker-compose exec app php artisan db:seed --force

Write-Host "ℹ️ Verificando versão do Laravel..."
docker-compose exec app php artisan about

Write-Host "✅ Sistema iniciado com sucesso!"
Write-Host ""
Write-Host "🌐 Aplicação disponível em: http://localhost:8080"
Write-Host "🗄️ phpMyAdmin disponível em: http://localhost:8081"
Write-Host "📊 API disponível em: http://localhost:8080/api"
Write-Host ""
Write-Host "Para parar os containers: docker-compose down"
