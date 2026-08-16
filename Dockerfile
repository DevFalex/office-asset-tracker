# Office Asset Tracker — PHP + Apache image for Render (and any Docker host).
FROM php:8.2-apache

# PostgreSQL driver used by the app (PDO pgsql) for Neon.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Apache: allow .htaccess overrides and enable URL rewriting (harmless default).
RUN a2enmod rewrite

# App lives in the office-asset-tracker/ subdirectory of the repo.
COPY office-asset-tracker/ /var/www/html/

# Send the site root to the login page.
RUN printf 'DirectoryIndex login.php index.php\n' > /etc/apache2/conf-available/oat-index.conf \
    && a2enconf oat-index

# Apache must listen on the port the platform provides ($PORT on Railway).
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
