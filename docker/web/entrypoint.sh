#!/usr/bin/env bash

if [ ! -f /var/www/drupal/credentials/public.key ]; then
    echo "oauth keys not found, generating..."
    mkdir -p /var/www/drupal/credentials
    openssl genrsa -out /var/www/drupal/credentials/private.key 2048
    openssl rsa -in /var/www/drupal/credentials/private.key -pubout > /var/www/drupal/credentials/public.key
    chmod 0755 /var/www/drupal/credentials
    chmod 0600 /var/www/drupal/credentials/private.key
    chmod 0600 /var/www/drupal/credentials/public.key
fi

chown -R www-data:www-data /var/www/drupal/credentials
chown -R www-data:www-data /var/www/drupal/files
chown -R www-data:www-data /var/www/drupal/web/sites/default/files
chown -R www-data:www-data /var/www/drupal/config

chown root:root /var/www/drupal/web/sites/default/services.yml
chown root:root /var/www/drupal/web/sites/default/settings.php
chmod 444 /var/www/drupal/web/sites/default/services.yml
chmod 444 /var/www/drupal/web/sites/default/settings.php

# Wait for MySQL
while ! mysqladmin ping -hdb --silent; do
    echo "Waiting for database connection..."
    sleep 5
done

drush --root=/var/www/drupal/web cc drush
drush --root=/var/www/drupal/web cr
drush --root=/var/www/drupal/web cim -y
drush --root=/var/www/drupal/web updatedb -y

exec "$@"
