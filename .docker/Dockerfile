FROM php:8.4-fpm


ARG UID=1000
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    git \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
        intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html


# Laravel precisa dessas pastas com permissão
RUN mkdir -p storage bootstrap/cache \
    && chown -R 1000:1000 /var/www/html

USER 1000:1000

CMD ["php-fpm"]
