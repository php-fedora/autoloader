<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
require_once __DIR__.'/../../../src'.'/autoload.php';

\Fedora\Autoloader\Autoload::addClassMap(
    array(
        'foo\\bar' => '/Bar.php',
    ),
    __DIR__
);
// @codeCoverageIgnoreEnd
