# Slim

Set up a Laravel project to console/api only.

- Console only
- Stateless API only

> **Note:** [laravel-console-starter](https://github.com/invokable/laravel-console-starter) is a starter kit that has this package applied.

## Requirements
- PHP >= 8.2
- Laravel >= 12.0

Only new projects with Laravel 12 or later.(No starter kit, No API)

## Installation

```shell
composer require revolution/laravel-slim --dev
```

### Uninstall
```shell
composer remove revolution/laravel-slim --dev
```

Once you have run the command you can uninstall this package.

## Usage

### Console project
Be sure to run this command only on new projects. A lot of files are deleted.

```shell
php artisan slim:console
```

Which files will be deleted? See SlimConsoleCommand.php

#### After set up
You can use the usual artisan commands.

```shell
php artisan make:command Test
```

#### Re-add config file

```shell
php artisan config:publish services
```

### Stateless API project (Sanctum API Token Authentication)
Be sure to run this command only on new projects.

```shell
php artisan slim:api
```

## LICENSE
MIT  
