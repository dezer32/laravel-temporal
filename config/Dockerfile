FROM dezer/php-roadrunner:8.2-2.12.1 as php-cli-prod

WORKDIR /var/app

COPY . /var/app

RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --no-dev \
    --ignore-platform-reqs


FROM php-cli-prod as php-cli-dev

RUN composer install \
        --no-interaction \
        --no-plugins \
        --no-scripts \
        --prefer-dist \
        --optimize-autoloader \
        --ignore-platform-reqs \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && printf 'xdebug.mode=develop,debug\n' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && printf 'xdebug.client_host="host.docker.internal"\n' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && printf 'xdebug.start_upon_error=yes\n' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && printf 'xdebug.start_with_request=yes\n' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && printf 'xdebug.log_level=0' >> /usr/local/etc/php/conf.d/xdebug.ini
