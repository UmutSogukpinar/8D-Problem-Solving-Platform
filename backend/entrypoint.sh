#!/bin/bash
set -e 

# Set mode: 'prod' or 'dev' (default: 'prod')
MODE="${1:-prod}"

echo "[INFO]: Backend Mode set to '$MODE'"

# ---------------------------------------------------------
# Install dependencies based on mode
# ---------------------------------------------------------
if [ "$MODE" = 'prod' ]; then
    echo "[INFO]: Production Mode: Only essential packages are being installed..."
    composer install --no-dev --optimize-autoloader --no-interaction
elif [ "$MODE" = 'dev' ]; then
    echo "[INFO]: Development Mode: All tools are being installed..."
    composer install --no-interaction
else
    echo "[ERROR]: Invalid mode specified: '$MODE'. Use 'prod' or 'dev'."
    exit 1
fi

# ---------------------------------------------------------
# Execute database migrations
# ---------------------------------------------------------
if [ -f "migrate.php" ]; then
    echo "[INFO]: Executing database migrations..."
    php migrate.php
else
    echo "[WARNING]: Migration script 'migrate.php' not found. Skipping migrations."
fi

# ---------------------------------------------------------
# Start PHP-FPM
# ---------------------------------------------------------
echo "[INFO]: Starting PHP-FPM..."
exec php-fpm -F