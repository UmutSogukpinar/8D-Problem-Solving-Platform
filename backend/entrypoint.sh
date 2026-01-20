#!/bin/bash

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
# Change file and group permissions
# ---------------------------------------------------------
echo "[INFO]: Fixing permissions for /var/www/html..."

chgrp -R www-data /var/www/html/logs
chmod -R 2775 /var/www/html/logs


# ---------------------------------------------------------
# Execute database migrations
# ---------------------------------------------------------
MIGRATION_FILE="database/migrate.php"

if [ -f "$MIGRATION_FILE" ]; then
    echo "[INFO]: Executing database migrations from $MIGRATION_FILE..."
    php "$MIGRATION_FILE"
else
    echo "[WARNING]: Migration script not found at '$MIGRATION_FILE'. Skipping."
fi

# ---------------------------------------------------------
# Execute database seeds
# ---------------------------------------------------------
SEED_FILE="database/seed.php"

if [ -f "$SEED_FILE" ]; then
    echo "[INFO]: Executing database seeds from $SEED_FILE..."
    php "$SEED_FILE"
else
    echo "[WARNING]: Seed script not found at '$SEED_FILE'. Skipping."
fi


# ---------------------------------------------------------
# Start PHP-FPM
# ---------------------------------------------------------
echo "[INFO]: Starting PHP-FPM..."
exec php-fpm -F