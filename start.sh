#!/bin/bash
set -e

echo "🚀 Iniciando Sistema de Biblioteca..."

echo "🧹 Parando e removendo containers e volumes antigos..."
docker-compose down -v

if [ ! -f .env ]; then
    echo "📋 Copiando arquivo .env..."
    cp .env.example .env
fi

echo "🐳 Construindo containers Docker..."
docker-compose build --no-cache

echo "🐳 Iniciando containers..."
docker-compose up -d

echo "⏳ Aguardando MySQL ficar disponível..."
until docker-compose exec db mysqladmin ping -h "127.0.0.1" --silent; do
  sleep 2
done

echo "⏳ Aguardando container app ficar disponível..."
until docker-compose exec app php -v > /dev/null 2>&1; do
  sleep 2
done

echo "⚙️ Criando pastas necessárias..."
docker-compose exec app mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions

echo "⚙️ Ajustando permissões..."
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache storage/framework
docker-compose exec app chmod -R 775 storage bootstrap/cache storage/framework

echo "📦 Instalando dependências do Laravel..."
docker-compose exec app composer install --no-dev --optimize-autoloader

echo "♻️ Limpando caches do Laravel..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
# docker-compose exec app php artisan view:clear

echo "🔑 Gerando chave da aplicação..."
docker-compose exec app php artisan key:generate

echo "🗄️ Executando migrations..."
docker-compose exec app php artisan migrate --force

echo "🌱 Executando seeders..."
docker-compose exec app php artisan db:seed --force

echo "ℹ️ Verificando versão do Laravel..."
docker-compose exec app php artisan about

echo "✅ Sistema iniciado com sucesso!"
echo ""
echo "🌐 Aplicação disponível em: http://localhost:8080"
echo "🗄️ phpMyAdmin disponível em: http://localhost:8081"
echo "📊 API disponível em: http://localhost:8080/api"
echo ""
echo "Para parar os containers: docker-compose down"
