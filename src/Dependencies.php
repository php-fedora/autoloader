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
    /* Already loaded dependencies */
    public static $_loaded = array();

    /**
     * Static functions only.
     */
    private function __construct()
    {
    }

    /**
     * Processes dependencies to require/load.
     *
     * - If dependency is not an array:
     *     - If dependency is required, it is only required/loaded if it exists,
     *       otherwise a `\RuntimeException` is thrown.
     *     - If dependency is not required, it is only required/loaded if it exists.
     * - If dependency is an array:
     *     - Loops through all items until the first item that exists is found and
     *       then it is required/loaded.  If no item is found and the dependency
     *       is required, the last item will be required/loaded if it exists,
     *       otherwise a `\RuntimeException` is thrown.
     *
     * See README for examples.
     *
     * @param array $dependencies Dependencies
     * @param bool  $required     Whether dependencies are required or not
     *
     * @throws \RuntimeException If required and no file is successfully loaded
     *                           via {@link requireFile()}
     */
    protected static function process(array $dependencies, $required)
    {
        foreach ($dependencies as $dependency) {
            if (is_array($dependency)) {
                $dependencyLoaded = false;

                foreach ($dependency as $firstExistsDependency) {
                    if (in_array($firstExistsDependency, self::$_loaded)) {
                        $dependencyLoaded = true;
                        break;
                    }
                }

                if (!$dependencyLoaded) {
                    foreach ($dependency as $firstExistsDependency) {
                        try {
                            requireFile($firstExistsDependency);
                            $dependencyLoaded = true;
                            self::$_loaded[] = $firstExistsDependency;
                            break;
                        } catch (\RuntimeException $e) {
                        }
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
                    self::$_loaded[] = $dependency;
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
     * @param array $requiredDependencies Required dependencies
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
     * @param array $optionalDependencies Optional dependencies
     *
     * @see process()
     */
    public static function optional(array $optionalDependencies)
    {
        static::process($optionalDependencies, false);
    }
}
