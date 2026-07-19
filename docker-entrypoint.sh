#!/bin/sh
set -e

# Laravel storage path inside the container
STORAGE_PATH="/app/storage"
DEFAULT_STORAGE_PATH="/app/default-storage"

# Check if storage directory is empty
if [ ! "$(ls -A $STORAGE_PATH)" ]; then
    echo "Storage directory is empty, initializing default structure..."
    cp -r $DEFAULT_STORAGE_PATH/* $STORAGE_PATH/
    chown -R www-data:www-data $STORAGE_PATH
    chmod -R 775 $STORAGE_PATH
else
    echo "Storage directory exists, skipping initialization."
fi

# Ensure correct permissions
php artisan storage:link
chown -R www-data:www-data /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

# Cache configuration
# php artisan cache:clear && \
php artisan route:clear && \
php artisan config:clear && \
php artisan view:clear && \
php artisan event:clear && \
php artisan optimize

# Run the application
exec "$@"
