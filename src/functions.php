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
 * Scope isolated require.
 *
 * Prevents access to a class' $this/self from required files.
 *
 * Originally taken from
 * {@link https://github.com/composer/composer/blob/master/src/Composer/Autoload/ClassLoader.php Composer/Autoload/ClassLoader::includeFile()}.
 *
 * @param string $file File to `require_once`.
 *
 * @throws \RuntimeException If file does not exist or is not readable.
 */
function requireFile($file)
{
    if (is_file($file) && is_readable($file)) {
        require_once $file;
    } else {
        throw new \RuntimeException("File not found: '$file'");
    }
}

/**
 * Scope isolated include.
 *
 * Prevents access to a class' $this/self from included files.
 *
 * Originally taken from
 * {@link https://github.com/composer/composer/blob/master/src/Composer/Autoload/ClassLoader.php Composer/Autoload/ClassLoader::includeFile()}.
 *
 * @param string $file File to `include_once`.
 */
function includeFile($file)
{
    include_once $file;
}
