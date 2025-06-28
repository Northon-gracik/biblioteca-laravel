#!/bin/bash
set -e

echo "⏳ Verificando dependências PHP (vendor)..."

if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "📦 Pasta vendor não encontrada, instalando dependências com composer..."
    composer install --no-interaction --optimize-autoloader
else
    echo "✅ Pasta vendor encontrada, pulando composer install."
fi

# Ajustar permissões para storage e cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "🚀 Iniciando comando: $@"
exec "$@"
