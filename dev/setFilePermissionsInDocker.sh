#!/bin/sh
set -e
cd /home/wwwroot/bamboocms-php || exit
chown -R www-data:www-data /home/wwwroot/bamboocms-php && \
find /home/wwwroot/bamboocms-php -type f -exec chmod 644 {} \; && \
find /home/wwwroot/bamboocms-php -type d -exec chmod 755 {} \; && \
chmod -R +777 /home/wwwroot/bamboocms-php/storage /home/wwwroot/bamboocms-php/bootstrap/cache && \
chmod -R +x /home/wwwroot/bamboocms-php/vendor/bin/ && \
chmod -R +x /home/wwwroot/bamboocms-php/dev/
