<?php
/**
 * This file is part of the Fedora Autoloader package.
 *
 * (c) Shawn Iwinski <shawn@iwin.ski> and Remi Collet <remi@fedoraproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fedora\Autoloader;

/**
 *
 */
function requireFile($file)
{
    require_once $file;
}

/**
 *
 */
function includeFile($file)
{
    include_once $file;
}
