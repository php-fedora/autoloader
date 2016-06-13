# Fedora Autoloaders

Standardized, simplified, and singleton autoloaders

## Autoloaders

### Symfony

#### Before

```
BuildRequires: php-composer(symfony/class-loader)
Requires:      php-composer(symfony/class-loader)

cat <<'AUTOLOAD' | tee src/autoload.php
<?php

if (!isset($fedoraClassLoader) || !($fedoraClassLoader instanceof \Symfony\Component\ClassLoader\ClassLoader)) {
    if (!class_exists('Symfony\\Component\\ClassLoader\\ClassLoader', false)) {
        require_once '%{_datadir}/php/Symfony/Component/ClassLoader/ClassLoader.php';
    }

    $fedoraClassLoader = new \Symfony\Component\ClassLoader\ClassLoader();
    $fedoraClassLoader->register();
}

// This library
$fedoraClassLoader->addPrefix('Foo\\Bar\\', dirname(dirname(__DIR__)));

// Required dependency
require_once '%{_datadir}/php/Foo/BazRequired/autoload.php';

// Optional dependency
if (file_exists('%{_datadir}/php/Foo/BazOptional/autoload.php')) {
    require_once '%{_datadir}/php/Foo/BazOptional/autoload.php';
}
AUTOLOAD
```

#### After

```
BuildRequires: php-composer(fedora/autoloader)
Requires:      php-composer(fedora/autoloader)

cat <<'AUTOLOAD' | tee src/autoload.php
<?php

// This library
require_once '%{_datadir}/php/Fedora/autoload.php';
\Fedora\Autoloader::addPsr4('Foo\\Bar\\', __DIR__);

// Dependencies
\Fedora\Autoloader::dependencies(array(
    '%{_datadir}/php/Foo/BazRequired/autoload.php' => true,
    '%{_datadir}/php/Foo/BazOptional/autoload.php' => false,
));
AUTOLOAD
```

### Zend

#### Before

```
BuildRequires: php-composer(zendframework/zend-loader)
Requires:      php-composer(zendframework/zend-loader)

cat <<'AUTOLOAD' | tee src/autoload.php
<?php

require_once '%{_datadir}/php/Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(
        'fallback_autoloader' => true, // for other dep, if needed
        'autoregister_zf' => true,     // for ZF, if needed
        'namespaces' => array(
           'Foo\\Bar\\' => __DIR__     // Your namespace
        )
    )
));

// Required dependency
require_once '%{_datadir}/php/Foo/BazRequired/autoload.php';

// Optional dependency
if (file_exists('%{_datadir}/php/Foo/BazOptional/autoload.php')) {
    require_once '%{_datadir}/php/Foo/BazOptional/autoload.php';
}
AUTOLOAD
```

#### After

```
BuildRequires: php-composer(fedora/autoloader)
Requires:      php-composer(fedora/autoloader)

cat <<'AUTOLOAD' | tee src/autoload.php
<?php

// This library
require_once '%{_datadir}/php/Fedora/autoload.php';
\Fedora\Autoloader::addPsr4('Foo\\Bar\\', __DIR__);

// Dependencies
\Fedora\Autoloader::dependencies(array(
    '%{_datadir}/php/Foo/BazRequired/autoload.php' => true,
    '%{_datadir}/php/Foo/BazOptional/autoload.php' => false,
));
AUTOLOAD
```

## Dependencies

### Before

```php
// Required dependencies
require_once '%{_datadir}/php/Foo1/autoload.php';
require_once '%{_datadir}/php/Foo2/autoload.php';

// Optional dependencies
foreach (array(
    '%{_datadir}/php/Baz1/autoload.php',
    '%{_datadir}/php/Baz2/autoload.php',
) as $optionalDependency) {
    if (file_exists($optionalDependency)) {
        require_once $optionalDependency;
    }
}
```

### After

```php
// Dependencies
\Fedora\Autoloader::dependencies(array(
    '%{_datadir}/php/Foo1/autoload.php' => true,
    '%{_datadir}/php/Foo2/autoload.php' => true,
    '%{_datadir}/php/Baz1/autoload.php' => false,
    '%{_datadir}/php/Baz2/autoload.php' => false,
));
```
