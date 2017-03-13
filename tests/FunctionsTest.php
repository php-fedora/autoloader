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

class FunctionsTest extends TestCase
{
    public function dataPath()
    {
        if (DIRECTORY_SEPARATOR == '/') {
            return array(
                array(true,  '/tmp',         'Linux full path'),
                array(false, 'foo/bar',      'Relative path'),
            );
        } else {
            return array(
                array(true,  'c:/tmp',       'Windows full path'),
                array(true,  '//foo/bar',    'UNC'),
                array(true,  '\\\\foo\\bar', 'UNC'),
                array(false, 'foo/bar',      'Relative path'),
            );
        }
    }

    /**
     * @dataProvider dataPath
     **/
    public function testIsFullPath($full, $path, $msg)
    {
        if ($full) {
            $this->assertTrue(\Fedora\Autoloader\isFullPath($path), $msg);
        } else {
            $this->assertFalse(\Fedora\Autoloader\isFullPath($path), $msg);
        }
    }
}
