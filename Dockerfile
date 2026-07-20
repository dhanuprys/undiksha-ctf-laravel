FROM dunglas/frankenphp:1-php8.4-alpine

# Install PHP extensions
RUN apk add --no-cache \
    git \
    zip \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    curl \
    && apk add --no-cache --virtual .build-deps \
    autoconf \
    gcc \
    g++ \
    make \
    && install-php-extensions \
    pcntl \
    zip \
    ctype \
    curl \
    dom \
    fileinfo \
    filter \
    hash \
    intl \
    mbstring \
    openssl \
    pcre \
    pdo \
    pdo_mysql \
    session \
    tokenizer \
    xml \
    gd \
    redis \
    && apk del .build-deps

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . /app
COPY storage/ /app/default-storage
COPY ./.deployments/php.ini-production /usr/local/etc/php/php.ini

RUN composer install \
    --classmap-authoritative \
    --no-interaction \
    --no-ansi \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    && composer clear-cache

RUN chmod +x ./docker-entrypoint.sh

ENTRYPOINT [ "/app/docker-entrypoint.sh" ]
CMD ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000", "--caddyfile=./.deployments/Caddyfile"]

EXPOSE 8000
