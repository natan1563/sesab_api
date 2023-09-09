# Use a imagem oficial do PHP 8.1-fpm baseada em Alpine
FROM php:8.1-fpm-alpine as sesab_app_api

WORKDIR /var/www/sesab_app

RUN echo http://dl-cdn.alpinelinux.org/alpine/edge/main >> /etc/apk/repositories
RUN echo http://dl-cdn.alpinelinux.org/alpine/edge/community/ >> /etc/apk/repositories

# Instalar o pacote curl
RUN apk --no-cache add curl
RUN apk add --no-cache bash

RUN apk add --no-cache bash

# Instala o composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala o pacote
RUN apk update && \
    apk add bash build-base gcc wget git autoconf libmcrypt-dev libzip-dev zip \
    g++ make openssl-dev \
    php81-openssl \
    php81-pdo_mysql \
    php81-mbstring

RUN pecl install mcrypt && \
    docker-php-ext-enable mcrypt

# Configurar o diretório de trabalho
COPY ../../. ./var/www/sesab_app

# Expor a porta utilizada pelo PHP-FPM
EXPOSE 8000

# Comando para iniciar o PHP-FPM
CMD ["php-fpm"]
