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

#### `phpab` Template

Template: [res/phpab/fedora.php.tpl](res/phpab/fedora.php.tpl)

For an example of its' usage, see [tests/genclassmap.sh](tests/genclassmap.sh).

## Dependencies loader

Loops through provided array of dependencies:
- If dependency is not an array:
    - If dependency is required, it is only required/loaded if it exists,
      otherwise a `\RuntimeException` is thrown.
    - If dependency is not required, it is only required/loaded if it exists.
- If dependency is an array:
    - Loops through all items until the first item that exists is found and
      then it is required/loaded.  If no item is found and the dependency
      is required, the last item will be required/loaded if it exists,
      otherwise a `\RuntimeException` is thrown.

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
if (
    is_file('/usr/share/php/RequiredFoo/autoload.php')
    && is_readable('/usr/share/php/RequiredFoo/autoload.php')
) {
    require_once '/usr/share/php/RequiredFoo/autoload.php';
} else {
    throw new \RuntimeException("File not found: '/usr/share/php/RequiredFoo/autoload.php'");
}

if (
    is_file('/usr/share/php/RequiredBar/autoload.php')
    && is_readable('/usr/share/php/RequiredBar/autoload.php')
) {
    require_once '/usr/share/php/RequiredBar/autoload.php';
} else {
    throw new \RuntimeException("File not found: '/usr/share/php/RequiredBar/autoload.php'");
}
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
if (
    is_file('/usr/share/php/RequiredFooVersion1/autoload.php')
    && is_readable('/usr/share/php/RequiredFooVersion1/autoload.php')
) {
    require_once '/usr/share/php/RequiredFooVersion1/autoload.php';
} elseif (
    is_file('/usr/share/php/RequiredFooVersion2/autoload.php')
    && is_readable('/usr/share/php/RequiredFooVersion2/autoload.php')
) {
    require_once '/usr/share/php/RequiredFooVersion2/autoload.php';
} elseif (
    is_file('/usr/share/php/RequiredFooVersion3/autoload.php')
    && is_readable('/usr/share/php/RequiredFooVersion3/autoload.php')
) {
    require_once '/usr/share/php/RequiredFooVersion3/autoload.php';
} else {
    throw new \RuntimeException("Files not found: "
        . "'/usr/share/php/RequiredFooVersion1/autoload.php'"
        . "|| '/usr/share/php/RequiredFooVersion2/autoload.php'"
        . "|| '/usr/share/php/RequiredFooVersion3/autoload.php'"
    );
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
if (
    is_file('/usr/share/php/OptionalFoo/autoload.php')
    && is_readable('/usr/share/php/OptionalFoo/autoload.php')
) {
    require_once '/usr/share/php/OptionalFoo/autoload.php';
}

if (
    is_file('/usr/share/php/OptionalBar/autoload.php')
    && is_readable('/usr/share/php/OptionalBar/autoload.php')
) {
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
if (
    is_file('/usr/share/php/OptionalFooVersion1/autoload.php')
    && is_readable('/usr/share/php/OptionalFooVersion1/autoload.php')
) {
    require_once '/usr/share/php/OptionalFooVersion1/autoload.php';
} elseif (
    is_file('/usr/share/php/OptionalFooVersion2/autoload.php')
    && is_readable('/usr/share/php/OptionalFooVersion2/autoload.php')
) {
    require_once '/usr/share/php/OptionalFooVersion2/autoload.php';
} elseif (
    is_file('/usr/share/php/OptionalFooVersion3/autoload.php')
    && is_readable('/usr/share/php/OptionalFooVersion3/autoload.php')
) {
    require_once '/usr/share/php/OptionalFooVersion3/autoload.php';
}
```

## License

[The MIT License (MIT)](LICENSE)
