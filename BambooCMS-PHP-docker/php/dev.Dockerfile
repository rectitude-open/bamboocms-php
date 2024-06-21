FROM php:8.3-apache

ENV APACHE_DOCUMENT_ROOT /home/wwwroot/BambooCMS-PHP/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

ARG TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN sed -i 's#deb.debian.org/debian$#mirrors.tuna.tsinghua.edu.cn/debian#' /etc/apt/sources.list.d/debian.sources

RUN apt-get clean
RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y \
    apt-utils \
    vim \
    wget \
    curl \
    git \
    htop

RUN apt-get install -y \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zlib1g-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    zip\
    cron

RUN service cron start
RUN update-rc.d cron defaults

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN docker-php-ext-install pdo pdo_mysql && \
    a2enmod rewrite
# composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

RUN yes | docker-php-ext-install mysqli && \
    docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ && \
    docker-php-ext-install gd zip pcntl bcmath && \
    docker-php-ext-enable opcache

RUN yes | pecl install xdebug && \
    docker-php-ext-enable xdebug


# * * * * * cd /var/www && /usr/local/bin/php artisan schedule:run >> /var/log/cron.log 2>&1
