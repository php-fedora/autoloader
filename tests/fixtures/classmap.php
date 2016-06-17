<?php
$vendor = '/usr/share/php';

if (!class_exists('Fedora\\Autoloader')) {
    require_once "$vendor/Fedora/Autoloader.php";
}

Fedora\Autoloader::addClassMap(
    array(
        'foo\\bar' => '/Bar.php'
    ),
    __DIR__
);

