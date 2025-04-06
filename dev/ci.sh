#!/bin/bash
set -e

php artisan test --parallel
/home/wwwroot/bamboocms-php/vendor/bin/pint
/home/wwwroot/bamboocms-php/vendor/bin/phpstan analyse
