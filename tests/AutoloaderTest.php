<?php
/**
 * This file is part of the Fedora Autoloader package.
 *
 * (c) Shawn Iwinski <shawn@iwin.ski> and Remi Collet <remi@fedoraproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fedora;

class AutoloaderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers Fedora::Autoloader::addPsr4
     **/
    public function testAddPsr4() {
        $this->assertFalse(class_exists('Foo\\Bar'));
        Autoloader::addPsr4('Foo', __DIR__ . '/fixtures/Foo');
        $this->assertTrue(class_exists('Foo\\Bar'));
    }

    /**
     * @covers Fedora::Autoloader::addClassMap
     **/
    public function testAddClassMap() {
        $this->assertFalse(class_exists('Foo\\Bar'));
        Autoloader::addClassMap(
            array(
                'foo\\bar' => '/Bar.php'
            ),
            __DIR__ . '/fixtures/Foo'
        );
        $this->assertTrue(class_exists('Foo\\Bar'));
    }

    /**
     * @covers Fedora::Autoloader::addClassMap
     **/
    public function testAddClassMapTemplate() {
        $this->assertFalse(class_exists('Foo\\Bar'));
        require __DIR__ . '/fixtures/Foo/classmap.php';
        $this->assertTrue(class_exists('Foo\\Bar'));
    }
}

