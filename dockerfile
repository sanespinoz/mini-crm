FROM php:8.2-fpm

# Instalar dependencias básicas y extensiones PHP necesarias
RUN apt update && apt install -y \
    libz-dev \
    libssl-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    git \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql pcntl \
    && pecl install redis \
    && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Instalar Node.js y npm (v20)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && node -v \
    && npm -v
