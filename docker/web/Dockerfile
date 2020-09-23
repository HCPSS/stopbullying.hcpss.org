FROM banderson/drupal:9

LABEL maintainer="brendan_anderson@hcpss.org"
LABEL vendor="Howard County Public School System"
LABEL org.hcpss.bullying.version="1.0.0"

#COPY drupal/web/autoload.php               /var/www/drupal/web/autoload.php
COPY d9/web/modules/custom             /var/www/drupal/web/modules/custom
COPY d9/web/themes/custom              /var/www/drupal/web/themes/custom
#COPY drupal/scripts                        /var/www/drupal/scripts
#COPY drupal/drush                          /var/www/drupal/drush
COPY d9/composer.json                  /var/www/drupal/composer.json
COPY d9/composer.lock                  /var/www/drupal/composer.lock
COPY d9/config                         /var/www/drupal/config
COPY d9/web/sites/default/settings.php /var/www/drupal/web/sites/default/settings.php
#COPY drupal/load.environment.php           /var/www/drupal/load.environment.php

RUN mkdir /var/www/drupal/files && chown www-data:www-data /var/www/drupal/files

RUN composer install -d /var/www/drupal

COPY docker/web/entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
EXPOSE 443

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
