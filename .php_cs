<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
    ->notName('classmap*.php')
;

return PhpCsFixer\Config::create()
    ->setFinder($finder)
;
