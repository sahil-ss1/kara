FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libmagickwand-dev \
    mariadb-client \
    supervisor
    
    
#RUN apt-get install -y git
#RUN apt-get -y install supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Install PHP extensions
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Mounted volumes
COPY ./ /var/www
COPY ./docker-deploy/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY ./docker-deploy/.env.docker /var/www/.env
COPY ./docker-deploy/kara-worker.conf /etc/supervisor/conf.d

# Set working directory
WORKDIR /var/www
RUN mkdir -p storage/app/public/avatars

RUN chown -R $user:$user ./
RUN chmod -R 775 composer.json composer.lock \
        composer.lock storage/framework/ \
        storage/logs/ bootstrap/cache/ storage/app/public/avatars/ /home/kara-user/.composer
RUN chown -R $(whoami):$(whoami) /var/log/
RUN chmod -R 775 /var/log

RUN composer config --no-plugins allow-plugins.pestphp/pest-plugin true
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
RUN php artisan storage:link
RUN php artisan key:generate --force
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
#RUN php artisan migrate:fresh --seed

USER $user

EXPOSE 9000
