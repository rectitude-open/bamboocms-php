#!/bin/sh
set -e
cd /home/wwwroot/BambooCMS-PHP || exit
chown -R www-data:www-data /home/wwwroot/BambooCMS-PHP && \
find /home/wwwroot/BambooCMS-PHP -type f -exec chmod 644 {} \; && \
find /home/wwwroot/BambooCMS-PHP -type d -exec chmod 755 {} \; && \
chmod -R +777 /home/wwwroot/BambooCMS-PHP/storage /home/wwwroot/BambooCMS-PHP/bootstrap/cache && \
chmod -R +x /home/wwwroot/BambooCMS-PHP/vendor/bin/ && \
chmod -R +x /home/wwwroot/BambooCMS-PHP/dev/
