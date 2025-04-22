FROM dunglas/frankenphp:php8.3

ENV SERVER_NAME=":80"

WORKDIR /app

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    zip \
    libzip-dev \
    libicu-dev \ 
    && docker-php-ext-install zip intl pdo_mysql \  
    && docker-php-ext-enable zip intl pdo_mysql 

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

COPY . /app

# Install PHP dependencies
RUN composer install

# Install NPM dependencies and build assets
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache