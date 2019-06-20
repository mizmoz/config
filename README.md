# [Mizmoz](https://www.mizmoz.com/) / Config

## Aims

- Be lightweight
- Configs are just php files which return arrays
- Environment set using .environment file
- Overridable by environment variables
- Lazy load config files

## Getting Started

### Composer Installation

```
composer require mizmoz/config
```

### Basic Usage

##### Set the environment

Create a file in the root of the project called .environment which should contain the current platform

To set as development:

`echo 'development' > /my/project/root/.environment`

Alternatively use system environment

`export ENVIRONMENT=development`

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
$config = Config::fromEnvironment(Environment::create(__DIR__));
$config->get('db.type'); // mongo
$config->get('db.hostname'); // localhost
```

##### Override the config values

```php
# Using the envrionment override to ensure any values that come from the environment variables are treated as priority
# Assuming we do something like:
# export MM_DB_PORT=3333
$config = new Config([
    'db' => [
        'default' => 'mysql',
        'port' => 3306,
    ]
]);

$config->addOverride(new Env);

# Access the value of 3333
$config->get('db.port');
```

```php
# The same can be done for cli arguments so:
# php my-script.php MM_DB_PORT=5555
$config = new Config([
    'db' => [
        'default' => 'mysql',
        'port' => 3306,
    ]
]);

$config->addOverride(new Args);

# Access the value of 5555
$config->get('db.port');
```

```php
# These can be chained with last priority so using the above example:
# Returns 5555
$config->addOverride(new Env)
    ->addOverride(new Args)
    ->get('db.port');

```

##### Accessing the configs

```php
$config = new Config([
    'db' => [
        'default' => 'mysql',
        'mysql' => 3306,
    ]
]);

# Basic accessing using dot notation
$config->get('db.default');

# Using the __invoke magic method
$config('db');

# Accessing with other config values referenced
$config->get('db.${db.default}');

# Accessing with relative references
$config->get('db.${.default}');
```

## Roadmap

- Add support for duplicating values such as website address which might be re-used for multiple params.
  - Need to figure out a way to do this without introducing any significant overhead when returning a config tree
