# [Mizmoz](https://www.mizmoz.com/) / Config

## Aims

- Be lightweight
- Configs are just php files which return arrays
- Environment set using .env file
- Lazy load config files

## Getting Started

### Composer Installation

```
composer require mizmoz/config
```

### Basic Usage

##### Load a configuration from an array

```php
$config = new Config(['app' => ...]);
$config->get('app...');
```

##### Load configuration from a directory of configs

```php
$config = Config::fromDirectory('./config', '.php');
$config->get('app...');
```

##### Load configuration from a directory whilst handling different environments

```php
# In a config db.php
return [
    'type' => 'mongo',
    'hostname' => 'db.servers.com',
];

# In another config file for development db.development.php
return \Mizmoz\Config\Extend::production('db', [
    'host' => 'localhost',
]);

# Setup the config from directory
$config = Config::fromEnvironment(new Environment(__DIR__));
$config->get('db.type'); // mongo
$config->get('db.hostname'); // localhost
```