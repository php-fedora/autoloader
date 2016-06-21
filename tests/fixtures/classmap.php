<?php
if (!class_exists('Fedora\\Autoloader')) {
    require_once "/usr/share/php/Fedora/autoload.php";
}

\Fedora\Autoloader::addClassMap(
    array(
        'foo\\bar' => '/Bar.php'
    ),
    __DIR__
);

