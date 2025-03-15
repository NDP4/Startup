FROM dunglas/frankenphp:php8.3

ENV SERVER_NAME=":80"

WORKDIR /app

COPY . /app

RUN apt-get update && apt-get install -y \
    zip \
    libzip-dev \
    libicu-dev \ 
    && docker-php-ext-install zip intl pdo_mysql \  
    && docker-php-ext-enable zip intl pdo_mysql 

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN composer install