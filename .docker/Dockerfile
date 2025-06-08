FROM php:8.3-fpm

WORKDIR /app

RUN apt-get update && apt-get install -y librabbitmq-dev supervisor \
    && docker-php-ext-install opcache pdo_mysql \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && rm -rf /var/lib/apt/lists/*

COPY . /app

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
