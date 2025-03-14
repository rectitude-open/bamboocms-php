#!/bin/bash
set -e

php artisan test
/home/wwwroot/bamboocms-php/vendor/bin/pint
/home/wwwroot/bamboocms-php/vendor/bin/phpstan analyse
