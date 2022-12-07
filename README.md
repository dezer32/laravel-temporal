# Integration temporal to laravel

## Introduce


This is an alternative implementation of Laravel's temporal.io integration.<br>
For Laravel 9.0.<br>
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

### Docker

Add docker configuration in project:

```bash
php artisan vendor:publish --tag=laravel-temporal-docker
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
