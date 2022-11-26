FROM php:8.1-cli as php-cli-prod

RUN apt-get update && apt-get install -y --no-install-recommends \
  nano \
  bash \
  libzip-dev \
  unzip \
  libonig-dev

# Install PHP Extensions
ENV CFLAGS="$CFLAGS -D_GNU_SOURCE"
RUN docker-php-ext-install zip \
  && docker-php-ext-install opcache sockets mbstring \
  && docker-php-ext-enable opcache sockets mbstring

# Protobuf and GRPC
ENV PROTOBUF_VERSION "3.21.9"
RUN pecl channel-update pecl.php.net
RUN pecl install protobuf-${PROTOBUF_VERSION} grpc \
    && docker-php-ext-enable protobuf grpc

# Install Temporal CLI
COPY --from=temporalio/admin-tools /usr/local/bin/tctl /usr/local/bin/tctl

# Install Composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Copy application codebase
WORKDIR /var/app
COPY . /var/app

RUN composer install

# Setup RoadRunner
RUN vendor/bin/rr get --no-interaction \
    && mv rr /usr/local/bin/rr \
    && chmod +x /usr/local/bin/rr

FROM php-cli-prod as php-cli-dev

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && printf 'xdebug.mode=develop,debug\n' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && printf 'xdebug.client_host="host.docker.internal"\n' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && printf 'xdebug.start_upon_error=yes\n' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && printf 'xdebug.start_with_request=yes\n' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && printf 'xdebug.log_level=0' >> /usr/local/etc/php/conf.d/xdebug.ini
