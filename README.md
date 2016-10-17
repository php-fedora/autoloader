# Fedora Autoloader

[![Build Status](https://travis-ci.org/php-fedora/autoloader.svg?branch=master)](https://travis-ci.org/php-fedora/autoloader)

Static [PSR-4](http://www.php-fig.org/psr/psr-4/), [PSR-0](http://www.php-fig.org/psr/psr-0/),
and classmap autoloader.  Includes loader for required and optional dependencies.

## Autoloader

### PSR-4

`\Fedora\Autoloader\Autoload::addPsr4($prefix, $path, $prepend = false)`

### PSR-0

`\Fedora\Autoloader\Autoload::addPsr0($prefix, $path, $prepend = false)`

### Classmap

`\Fedora\Autoloader\Autoload::addClassMap(array $classMap, $path)`

## Dependencies loader

Loops through provided array of dependencies:
- If dependency is not an array:
    - If dependency is required, it is always required/loaded.
    - If dependency is not required, it is only required/loaded if it exists.
- If dependency is an array:
    - Loops through all items until the first item that exists is found and
        then it is required/loaded.  If no item is found and the dependency
        is required, the last item will be required/loaded (causing an error).

### Required dependencies

`\Fedora\Autoloader\Dependencies::required(array $requiredDependencies)`

#### Example 1

```php
\Fedora\Autoloader\Dependencies::required(array(
    '/usr/share/php/RequiredFoo/autoload.php',
    '/usr/share/php/RequiredBar/autoload.php',
));
```

Equates to:

```php
require_once '/usr/share/php/RequiredFoo/autoload.php';
require_once '/usr/share/php/RequiredBar/autoload.php';
```

#### Example 2

```php
\Fedora\Autoloader\Dependencies::required(array(
    array(
      '/usr/share/php/RequiredFooVersion1/autoload.php',
      '/usr/share/php/RequiredFooVersion2/autoload.php',
      '/usr/share/php/RequiredFooVersion3/autoload.php',
    ),
));
```

Equates to:

```php
if (file_exists('/usr/share/php/RequiredFooVersion1/autoload.php')) {
    require_once '/usr/share/php/RequiredFooVersion1/autoload.php';
} elseif (file_exists('/usr/share/php/RequiredFooVersion2/autoload.php')) {
    require_once '/usr/share/php/RequiredFooVersion2/autoload.php';
} else {
    require_once '/usr/share/php/RequiredFooVersion3/autoload.php';
}
```

### Optional dependencies

`\Fedora\Autoloader\Dependencies::optional(array $optionalDependencies)`

#### Example 1

```php
\Fedora\Autoloader\Dependencies::optional(array(
    '/usr/share/php/OptionalFoo/autoload.php',
    '/usr/share/php/OptionalBar/autoload.php',
));
```

Equates to:

```php
if (file_exists('/usr/share/php/OptionalFoo/autoload.php')) {
    require_once '/usr/share/php/OptionalFoo/autoload.php';
}

if (file_exists('/usr/share/php/OptionalBar/autoload.php')) {
    require_once '/usr/share/php/OptionalBar/autoload.php';
}
```

#### Example 2

```php
\Fedora\Autoloader\Dependencies::optional(array(
    array(
      '/usr/share/php/OptionalFooVersion1/autoload.php',
      '/usr/share/php/OptionalFooVersion2/autoload.php',
      '/usr/share/php/OptionalFooVersion3/autoload.php',
    ),
));
```

Equates to:
```php
if (file_exists('/usr/share/php/OptionalFooVersion1/autoload.php')) {
    require_once '/usr/share/php/OptionalFooVersion1/autoload.php';
} elseif (file_exists('/usr/share/php/OptionalFooVersion2/autoload.php')) {
    require_once '/usr/share/php/OptionalFooVersion2/autoload.php';
} elseif (file_exists('/usr/share/php/OptionalFooVersion3/autoload.php')) {
    require_once '/usr/share/php/OptionalFooVersion3/autoload.php';
}
```

## License

[The MIT License (MIT)](LICENSE)
