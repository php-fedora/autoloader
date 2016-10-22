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
     * @group psr4
     **/
    public function testAddPsr4()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        Autoload::addPsr4('Foo', __DIR__.'/fixtures/Foo');
        $this->assertTrue(class_exists('Foo\\Bar'));
    }

    /**
     * @group psr4
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
     * @group classmap
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
     * @group classmap
     **/
    public function testAddClassMapTemplate()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        require __DIR__.'/fixtures/Foo/classmap.php';
        $this->assertTrue(class_exists('Foo\\Bar'));
    }

    /**
     * @group classmap
     **/
    public function testAddClassMapLowerCase()
    {
        $this->assertFalse(class_exists('foo\\bar'));
        require __DIR__.'/fixtures/Foo/classmap.php';
        $this->assertTrue(class_exists('foo\\bar'));
    }

    /**
     * @group classmap
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
     * @group classmap
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

    /**
     * @group psr0
     **/
    public function testAddIncludePath()
    {
        // Check if PEAR is installed
        $pear = false;
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $p) {
            if (file_exists("$p/PEAR.php")) {
                $pear = true;
                break;
            }
        }
        if (!$pear) {
            $this->markTestSkipped('PEAR not found in include_path');
        }
        $this->assertFalse(class_exists('PEAR'));
        $this->assertFalse(class_exists('PEAR_Installer_Role_Cfg'));

        Autoload::addIncludePath();

        $this->assertTrue(class_exists('PEAR'));
        $this->assertTrue(class_exists('PEAR_Installer_Role_Cfg'));
    }

    /**
     * @group psr0
     **/
    public function testAddPsr0Simple()
    {
        $this->assertFalse(class_exists('Foo'));
        $this->assertFalse(class_exists('Foo_Bar'));
        $this->assertFalse(class_exists('One\\Two\\Foo'));
        $this->assertFalse(class_exists('One_Two\\Foo'));

        Autoload::addPsr0('', __DIR__.'/fixtures/PSR0');

        $this->assertTrue(class_exists('Foo'));
        $this->assertTrue(class_exists('Foo_Bar'));
        $this->assertTrue(class_exists('One\\Two\\Foo'));
        $this->assertTrue(class_exists('One_Two\\Foo'));
    }

    /**
     * @group psr0
     **/
    public function testAddPsr0ns1()
    {
        $this->assertFalse(class_exists('One\\Two\\Foo'));

        Autoload::addPsr0('One\\', __DIR__.'/fixtures/PSR0');

        $this->assertTrue(class_exists('One\\Two\\Foo'));
    }

    /**
     * @group psr0
     **/
    public function testAddPsr0ns2()
    {
        $this->assertFalse(class_exists('One\\Two\\Foo'));

        Autoload::addPsr0('One\\Two\\', __DIR__.'/fixtures/PSR0');

        $this->assertTrue(class_exists('One\\Two\\Foo'));
    }
}
