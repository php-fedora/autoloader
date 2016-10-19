<?php

if (!class_exists('Fedora\\Autoloader\\Autoload', false)) {
    require_once ___AUTOLOAD_PATH___.'/autoload.php';
}

\Fedora\Autoloader\Autoload::addClassMap(
    array(
        ___CLASSLIST___
    ),
    __DIR__
);
