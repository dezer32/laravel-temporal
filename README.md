# Integration temporal to laravel

## Introduce


This is an alternative implementation of Laravel's temporal.io integration.<br>
Older link: [laravel-temporal-project.git](https://github.com/dezer32/laravel-temporal-project.git)

## Installation

### 1. Install package

```bash
composer require dezer32/laravel-temporal
```

### 2. Add vars to .env file

```dotenv
TEMPORAL_CLI_ADDRESS=temporal:7233
```

### 3. ...

### 4. Profit!

## Usage

```php
<?php

declare(strict_types=1);

namespace Dezer32\Temporal\Laravel\Example\Providers;

use Dezer32\Temporal\Laravel\Core\Providers\TemporalServiceProvider;

class ExampleTemporalServiceProvider extends TemporalServiceProvider
{
    protected array $activityBindings = [];
    protected array $workflowBindings = [];
}
```

Demo usage: [demo-laravel-temporal.git](https://github.com/dezer32/demo-laravel-temporal.git)

## Other

### .rr.yaml

```yaml
version: "2.7"

rpc:
  listen: "tcp://127.0.0.1:6001"

server:
  command: php ${WORKING_DIR}/artisan temporal-project:server:init

http:
  address: 0.0.0.0:8000
  pool:
    num_workers: 4

temporal:
  address: ${TEMPORAL_CLI_ADDRESS}
  activities:
    command: "php ${WORKING_DIR}/artisan temporal-project:workflow:init"
    num_workers: 4

logs:
  level: debug
  mode: development
```

### Dockerfile

```dockerfile
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
```

### docker-compose.yaml

```yaml
version: "3.8"

services:
  app:
    build: .
    depends_on:
      - temporal
    environment:
      WORKING_DIR: ${WORKING_DIR}
      TEMPORAL_CLI_ADDRESS: ${TEMPORAL_CLI_ADDRESS}
    command: [ "/usr/local/bin/rr", "serve", "-c", ".rr.yaml" ]
    ports:
      - "8000:8000"
    volumes:
      - ./:/var/app

  database:
    image: postgres:13
    environment:
      POSTGRES_USER: '${DB_USERNAME}'
      POSTGRES_PASSWORD: '${DB_PASSWORD}'
    ports:
      - "3306:3306"
    volumes:
      - tp_database_data:/var/lib/postgresql/data

  temporal:
    image: temporalio/auto-setup:${TEMPORAL_VERSION}
    depends_on:
      - database
    environment:
      DB: postgresql
      DB_PORT: ${DB_PORT}
      POSTGRES_USER: ${DB_USERNAME}
      POSTGRES_PWD: ${DB_PASSWORD}
      POSTGRES_SEEDS: database
    ports:
      - "7233:7233"
    volumes:
      - tp_dynamic_config_data:/etc/temporal/config/dynamicconfig

  temporal-admin-tools:
    image: temporalio/admin-tools:${TEMPORAL_VERSION}
    depends_on:
      - temporal
    environment:
      TEMPORAL_CLI_ADDRESS: ${TEMPORAL_CLI_ADDRESS}
    stdin_open: true
    tty: true

  temporal-ui:
    image: temporalio/ui:${TEMPORAL_UI_VERSION}
    depends_on:
      - temporal
    environment:
      TEMPORAL_ADDRESS: ${TEMPORAL_CLI_ADDRESS}
      TEMPORAL_CORS_ORIGINS: ${TEMPORAL_CORS_ORIGINS}
    ports:
      - "8080:8080"

volumes:
  tp_dynamic_config_data:
    driver: local
  tp_database_data:
    driver: local
```

### .env

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=temporal-project
DB_USERNAME=temporal-project
DB_PASSWORD=temporal-project

WORKING_DIR=/var/app
TEMPORAL_VERSION=1.18
TEMPORAL_UI_VERSION=2.8.3
TEMPORAL_CLI_ADDRESS=temporal:7233
TEMPORAL_CORS_ORIGINS=http://localhost:3000
```

### composer.json

```json
{
    ...
	"repositories": [
		{
			"type": "git",
			"url": "git@github.com:dezer32/laravel-temporal.git"
		}
	],
    ...
}
```
