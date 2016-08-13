<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
    ->exclude('vendor')
    ->notName('classmap*.php')
;

return Symfony\CS\Config\Config::create()
    ->finder($finder)
;
