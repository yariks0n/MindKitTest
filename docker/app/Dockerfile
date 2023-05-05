FROM php:8.1.13-fpm

# Устанавливаем пакеты ОС.
RUN apt-get update && apt-get install -y wget git zip unzip pkg-config librabbitmq-dev libpq-dev libxml2-dev libxslt1-dev libzip-dev \
    libpng-dev libonig-dev libfreetype6-dev libjpeg62-turbo-dev libxml2-dev \
    && apt-get clean && apt-get autoclean

RUN docker-php-source extract \
    && mkdir /usr/src/php/ext/amqp \
    && curl -L https://github.com/php-amqp/php-amqp/archive/master.tar.gz | tar -xzC /usr/src/php/ext/amqp --strip-components=1

# Устанавливаем расширения php.
RUN docker-php-ext-install -j$(nproc) bcmath pdo pdo_pgsql mysqli gd zip exif soap mbstring pcntl sockets xsl amqp

# Устанавливаем composer.
COPY --from=composer:2.2.6 /usr/bin/composer /usr/local/bin/composer

# Конфиг php.
ADD ./php.ini /usr/local/etc/php/conf.d/90-php.ini
ADD ./overrides.conf /usr/local/etc/php-fpm.d/zz-overrides.conf

# Меняем пользователя.
RUN usermod -u 1000 www-data
USER www-data

WORKDIR "/var/www"

CMD ["sh", "-c", "composer install --no-interaction --ignore-platform-reqs && php-fpm"]

EXPOSE 9000