FROM php:8.2-fpm

# Arguments définis dans docker-compose.yml
ARG user
ARG uid

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    supervisor \
    nginx \
    libpq-dev \
    postgresql-client

# Nettoyer le cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP pour PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Obtenir la dernière version de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration du répertoire de travail
WORKDIR /var/www/ISIBURGER/

# Copie du code de l'application
COPY . /var/www/ISIBURGER/

# Copie de la configuration nginx
COPY docker/nginx/conf.d/app.conf /etc/nginx/sites-available/default

# Installation des dépendances de l'application
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Génération de la clé d'application (si nécessaire)
RUN if [ ! -f ".env" ]; then \
    cp .env.example .env && \
    php artisan key:generate; \
    fi

# Optimisations pour la production
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/ISIBURGER/storage /var/www/ISIBURGER/bootstrap/cache

# Exposition du port 80
EXPOSE 80

# Configuration du point d'entrée
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Démarrage de Nginx et PHP-FPM via Supervisor
CMD ["/usr/bin/supervisord", "-c", "/var/www/ISIBURGER/docker/supervisor/supervisord.conf"]