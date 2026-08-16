#!/bin/sh
set -e

# Railway (and similar hosts) inject the public port via $PORT. Apache
# defaults to 80, so rewrite its listen directive and vhost to match.
PORT="${PORT:-80}"

sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
