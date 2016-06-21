<?php
if (!class_exists('Fedora\\Autoloader')) {
    require_once "/usr/share/php/Fedora/autoload.php";
}

\Fedora\Autoloader::addClassMap(
    array(
        'foo\\bar' => '/Bar3.php'
    ),
    __DIR__
);

