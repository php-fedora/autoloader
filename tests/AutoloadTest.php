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

use Fedora\Autoloader\Autoload;

class AutoloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Fedora::Autoloader::Autoload::addPsr4
     **/
    public function testAddPsr4()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        Autoload::addPsr4('Foo', __DIR__.'/fixtures/Foo');
        $this->assertTrue(class_exists('Foo\\Bar'));
    }

    /**
     * @covers Fedora::Autoloader::Autoload::addPsr4
     **/
    public function testAddPsr4Order()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        Autoload::addPsr4('Foo', __DIR__.'/fixtures/Foo2');
        Autoload::addPsr4('Foo', __DIR__.'/fixtures/Foo');

        // Ensure first loaded is used
        $this->assertEquals('two', \Foo\Bar::order);
    }

    /**
     * @covers Fedora::Autoloader::Autoload::addClassMap
     **/
    public function testAddClassMap()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        Autoload::addClassMap(
            array(
                'foo\\bar' => '/Bar.php',
            ),
            __DIR__.'/fixtures/Foo'
        );
        $this->assertTrue(class_exists('Foo\\Bar'));
    }

    /**
     * @covers Fedora::Autoloader::Autoload::addClassMap
     **/
    public function testAddClassMapTemplate()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        require __DIR__.'/fixtures/Foo/classmap.php';
        $this->assertTrue(class_exists('Foo\\Bar'));
    }

    /**
     * @covers Fedora::Autoloader::Autoload::addClassMap
     **/
    public function testAddClassMapLowerCase()
    {
        $this->assertFalse(class_exists('foo\\bar'));
        require __DIR__.'/fixtures/Foo/classmap.php';
        $this->assertTrue(class_exists('foo\\bar'));
    }

    /**
     * @covers Fedora::Autoloader::Autoload::addClassMap
     **/
    public function testAddClassMapTemplateOrder()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        require __DIR__.'/fixtures/Foo/classmap.php';
        require __DIR__.'/fixtures/Foo2/classmap.php';

        // Ensure first loaded is used
        $this->assertEquals('one', \Foo\Bar::order);
    }

    /**
     * @covers Fedora::Autoloader::Autoload::addClassMap
     **/
    public function testAddClassMapTemplateOrderBis()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        require __DIR__.'/fixtures/Foo2/classmap2.php';
        require __DIR__.'/fixtures/Foo/classmap.php';
        require __DIR__.'/fixtures/Foo2/classmap.php';

        // Ensure first loaded is used
        $this->assertEquals('three', \Foo\Bar::order);

        $classmap = Autoload::getClassMap();
        $this->assertEquals(2, count($classmap));
        $this->assertArrayHasKey(__DIR__.'/fixtures/Foo', $classmap);
        $this->assertArrayHasKey(__DIR__.'/fixtures/Foo2', $classmap);
    }
}
