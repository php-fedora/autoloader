<?php

if (!class_exists('Fedora\\Autoloader\\Autoload', false)) {
    require_once __DIR__.'/../../../src'.'/autoload.php';
}

\Fedora\Autoloader\Autoload::addClassMap(
    array(
        'foo\\bar' => '/Bar.php',
                'foo\\bar\\baz' => '/Bar/Baz.php'
    ),
    __DIR__
);
