#!/bin/bash

# Attendre que la base de données PostgreSQL soit prête
if [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "Attente de la disponibilité de PostgreSQL..."
    until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME"; do
        echo "PostgreSQL n'est pas encore prêt - attente..."
        sleep 2
    done
    echo "PostgreSQL est prêt!"
fi

# Exécuter les migrations
php artisan migrate --force

# Exécuter les seeders si nécessaire
if [ "$APP_ENV" = "production" ] && [ "$SEED_DATABASE" = "true" ]; then
    php artisan db:seed --force
fi

# Démarrer la file d'attente (queue) en arrière-plan pour les notifications par email
php artisan queue:work --daemon &

# Démarrer supervisord
exec "$@"