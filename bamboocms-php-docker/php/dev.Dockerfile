FROM php:8.3-fpm

ARG WORKDIR=/home/wwwroot/bamboocms-php
ENV DOCUMENT_ROOT=${WORKDIR}
ENV NODE_VERSION=20.x

ARG GROUP_ID=1000
ARG USER_ID=1000
ENV USER_NAME=www-data
ARG GROUP_NAME=www-data

ARG TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN service cron start
RUN update-rc.d cron defaults

RUN apt-get clean
RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y \
    apt-utils \
    vim \
    wget \
    git \
    curl \
    htop \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zlib1g-dev \
    libmemcached-dev \
    libzip-dev \
    libpng-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    librdkafka-dev \
    libpq-dev \
    openssh-server \
    zip \
    unzip \
    supervisor \
    sqlite3  \
    nano \
    cron

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION} | sudo bash -
RUN apt-get install -y nodejs

RUN apt-get install -y nginx

RUN yes | docker-php-ext-install mysqli && \
    docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ && \
    docker-php-ext-install gd zip pcntl bcmath mbstring exif && \
    docker-php-ext-enable opcache

RUN yes | pecl install xdebug && \
    docker-php-ext-enable xdebug

