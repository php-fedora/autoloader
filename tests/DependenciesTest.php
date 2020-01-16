<?php
/**
 * This file is part of the Fedora Autoloader package.
 *
 * (c) Shawn Iwinski <shawn@iwin.ski> and Remi Collet <remi@fedoraproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fedora\Autoloader\Test;

use PHPUnit\Framework\TestCase;
use Fedora\Autoloader\Dependencies;

class DependenciesTest extends TestCase
{
    public function testRequiredExists()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        $this->assertFalse(class_exists('Foo\\Bar\\Baz'));

        Dependencies::required(array(
            __DIR__.'/fixtures/Foo/Bar.php',
            __DIR__.'/fixtures/Foo/Bar/Baz.php',
        ));

        $this->assertTrue(class_exists('Foo\\Bar'));
        $this->assertTrue(class_exists('Foo\\Bar\\Baz'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegex /Files not found.*DoesNotExist.*AlsoNotExist/
     **/
    public function testRequiredNotExistsLast()
    {
        Dependencies::required(array(
            array(
                __DIR__.'/fixtures/DoesNotExist.php',
                __DIR__.'/fixtures/AlsoNotExist.php',
            ),
        ));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegex /File not found.*DoesNotExist/
     **/
    public function testRequiredNotExists()
    {
        Dependencies::required(array(
            __DIR__.'/fixtures/DoesNotExist.php',
        ));
    }

    public function testRequiredFirstExists()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        $this->assertFalse(class_exists('Foo\\Bar\\Baz'));

        Dependencies::required(array(
            array(
                __DIR__.'/fixtures/DoesNotExist.php',
                __DIR__.'/fixtures/Foo/Bar.php',
                __DIR__.'/fixtures/Foo/Bar/Baz.php',
            ),
        ));

        $this->assertTrue(class_exists('Foo\\Bar'));
        $this->assertFalse(class_exists('Foo\\Bar\\Baz'));
    }

    public function testRequiredAlreadyLoaded()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        $this->assertFalse(class_exists('Foo\\Bar\\Baz'));

        Dependencies::required(array(
            __DIR__.'/fixtures/Foo/Bar/Baz.php',
        ));

        $this->assertTrue(class_exists('Foo\\Bar\\Baz'));

        Dependencies::required(array(
            array(
                __DIR__.'/fixtures/Foo/Bar.php',
                __DIR__.'/fixtures/Foo/Bar/Baz.php',
            ),
        ));

        $this->assertFalse(class_exists('Foo\\Bar'));
    }

    public function testOptionalExists()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        $this->assertFalse(class_exists('Foo\\Bar\\Baz'));

        Dependencies::optional(array(
            __DIR__.'/fixtures/DoesNotExist.php',
            __DIR__.'/fixtures/Foo/Bar.php',
            __DIR__.'/fixtures/Foo/Bar/Baz.php',
        ));

        $this->assertTrue(class_exists('Foo\\Bar'));
        $this->assertTrue(class_exists('Foo\\Bar\\Baz'));
    }

    /**
     * Just testing that no error/exception is thrown here
     */
    public function testOptionalNotExists()
    {
        Dependencies::optional(array(__DIR__.'/fixtures/DoesNotExist.php'));
        $this->assertTrue(true);
    }

    public function testOptionalFirstExists()
    {
        $this->assertFalse(class_exists('Foo\\Bar'));
        $this->assertFalse(class_exists('Foo\\Bar\\Baz'));

        Dependencies::optional(array(
            array(
                __DIR__.'/fixtures/DoesNotExist.php',
                __DIR__.'/fixtures/Foo/Bar.php',
                __DIR__.'/fixtures/Foo/Bar/Baz.php',
            ),
        ));

        $this->assertTrue(class_exists('Foo\\Bar'));
        $this->assertFalse(class_exists('Foo\\Bar\\Baz'));
    }

    public function testIncludePathRequired()
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

        Dependencies::required(array(
            'PEAR.php',
        ));

        $this->assertTrue(class_exists('PEAR'));
    }

    /**
     * @depends testIncludePathRequired
     **/
    public function testIncludePathOptional()
    {
        $this->assertFalse(class_exists('PEAR'));

        Dependencies::optional(array(
            'PEAR.php',
        ));

        $this->assertTrue(class_exists('PEAR'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegex /File not found.*DoesNotExist/
     **/
    public function testIncludePathNotFound()
    {
        Dependencies::required(array(
            'DoesNotExist.php',
        ));
    }
}
