#!/bin/bash

cd $(dirname $0)
cd ..

vendor/bin/phpab \
    --template res/autoload.template \
    --var "PATH=__DIR__.'/../../../src'" \
    --output tests/fixtures/Foo/classmap.php \
    tests/fixtures/Foo

vendor/bin/phpab \
    --template res/autoload.template \
    --var "PATH=__DIR__.'/../../../src'" \
    --output tests/fixtures/Foo2/classmap.php \
    tests/fixtures/Foo2/Bar.php

vendor/bin/phpab \
    --template res/autoload.template \
    --var "PATH=__DIR__.'/../../../src'" \
    --output tests/fixtures/Foo2/classmap2.php \
    tests/fixtures/Foo2/Bar3.php
