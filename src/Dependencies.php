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

class Dependencies
{
    /**
     * Static functions only.
     */
    private function __construct()
    {
    }

    /**
     * Processes dependencies to require/load.
     *
     * Loops through all $dependencies:
     * - If dependency is not an array:
     *     - If dependency is required, it is always required/loaded.
     *     - If dependency is not required, it is only required/loaded if it
     *       exists.
     * - If dependency is an array, loops through all items until the first
     *   item that exists is found and then it is required/loaded.  If no item
     *   is found and the dependency is required, the last item will be
     *   required/loaded (causing an error).
     *
     * Example 1 (required):
     *
     * ```php
     * process(array(
     *     '/usr/share/php/Foo/autoload.php',
     *     '/usr/share/php/Bar/autoload.php',
     * ), true);
     * ```
     *
     * `/usr/share/php/Foo/autoload.php` and `/usr/share/php/Bar/autoload.php`
     * are always required/loaded.
     *
     * Example 2 (optional):
     *
     * ```php
     * process(array(
     *     '/usr/share/php/Foo/autoload.php',
     *     '/usr/share/php/Bar/autoload.php',
     * ), false);
     * ```
     *
     * `/usr/share/php/Foo/autoload.php` is only required/loaded if it exists.
     * `/usr/share/php/Bar/autoload.php` is only required/loaded if it exists.
     *
     * Example 3 (required, first exists):
     *
     * ```php
     * process(array(
     *     array(
     *         '/usr/share/php/Foo/autoload.php',
     *         '/usr/share/php/Bar/autoload.php',
     *     )
     * ), true);
     * ```
     *
     * - If `/usr/share/php/Foo/autoload.php` exists, it is required/loaded and
     *   `/usr/share/php/Bar/autoload.php` is skipped.
     * - If `/usr/share/php/Foo/autoload.php` does not exist, it is skipped. If
     *   `/usr/share/php/Bar/autoload.php` exists, it is required/loaded.
     * - If neither `/usr/share/php/Foo/autoload.php` nor `/usr/share/php/Bar/autoload.php`
     *   exist, `/usr/share/php/Bar/autoload.php` is required/loaded and an
     *   error occurs.
     *
     * Example 4 (optional, first exists):
     *
     * ```php
     * process(array(
     *     array(
     *         '/usr/share/php/Foo/autoload.php',
     *         '/usr/share/php/Bar/autoload.php',
     *     )
     * ), true);
     * ```
     *
     * - If `/usr/share/php/Foo/autoload.php` exists, it is required/loaded and
     *   `/usr/share/php/Bar/autoload.php` is skipped.
     * - If `/usr/share/php/Foo/autoload.php` does not exist, it is skipped. If
     *   `/usr/share/php/Bar/autoload.php` exists, it is required/loaded.
     * - If neither `/usr/share/php/Foo/autoload.php` nor `/usr/share/php/Bar/autoload.php`
     *   exist, nothing occurs.
     *
     * @param array $dependencies Dependencies
     * @param bool  $required     Whether dependencies are required or not.
     *
     * @throws \RuntimeException If required and no file is successfully loaded
     *     via {@link requireFile()}.
     */
    protected static function process(array $dependencies, $required)
    {
        foreach ($dependencies as $dependency) {
            if (is_array($dependency)) {
                $dependencyLoaded = false;

                foreach ($dependency as $firstExistsDependency) {
                    try {
                        requireFile($firstExistsDependency);
                        $dependencyLoaded = true;
                        break;
                    } catch (\RuntimeException $e) {
                    }
                }

                if ($required && !$dependencyLoaded) {
                    throw new \RuntimeException(sprintf(
                      "Files not found: '%s'",
                      implode("' || '", $dependency)
                    ));
                }
            } else {
                try {
                    requireFile($dependency);
                } catch (\RuntimeException $e) {
                    if ($required) {
                        throw $e;
                    }
                }
            }
        }
    }

    /**
     * Requires dependency files.
     *
     * Calls `process($requiredDependencies, true)`.
     *
     * @param array $requiredDependencies Required dependencies.
     *
     * @see process()
     */
    public static function required(array $requiredDependencies)
    {
        static::process($requiredDependencies, true);
    }

    /**
     * Optionally requires other dependency files if they exist.
     *
     * Calls `process($optionalDependencies, false)`.
     *
     * @param array $optionalDependencies Optional dependencies.
     *
     * @see process()
     */
    public static function optional(array $optionalDependencies)
    {
        static::process($optionalDependencies, false);
    }
}
