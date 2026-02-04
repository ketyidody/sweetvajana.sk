#!/bin/bash
set -e

# Create .env file if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env file from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Wait for database to be ready
echo "Waiting for database connection..."
max_tries=30
counter=0
until php -r "new PDO('mysql:host=$DB_HOST;port=$DB_PORT', '$DB_USERNAME', '$DB_PASSWORD');" 2>/dev/null; do
    counter=$((counter + 1))
    if [ $counter -gt $max_tries ]; then
        echo "Error: Database connection failed after $max_tries attempts"
        exit 1
    fi
    echo "Waiting for database... (attempt $counter/$max_tries)"
    sleep 2
done
echo "Database is ready!"

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Ensure storage symlink points to container path
php artisan storage:link --force 2>/dev/null || true

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear and cache config for production
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Execute the main command
exec "$@"
