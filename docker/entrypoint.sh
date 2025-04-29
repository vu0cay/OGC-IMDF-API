#!/bin/bash

# Wait for DB to be ready
echo "Waiting for PostgreSQL..."
until nc -z db 5432; do
  echo "Database is unavailable - sleeping"
  sleep 2
done

# Run Laravel migrations
echo "Running migrate..."
php artisan migrate --force

# Start Apache
exec apache2-foreground