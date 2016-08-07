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

class Autoload
{
    /**
     * @var bool Whether self is registered as an autoloader.
     */
    protected static $registered = false;

    /**
     * @var array Class map. See addClassMap() for description of elements.
     */
    protected static $classMap = array();

    /**
     * @var array PSR-4 mapping stack. See addPsr4() for description of elements.
     */
    protected static $psr4 = array();

    /**
     * Static functions only.
     */
    private function __construct()
    {
    }

    /**
     * Returns if self is registered as an autoloader.
     *
     * @return bool
     */
    public static function isRegistered()
    {
        return static::$registered;
    }

    /**
     * Register self as an autoloader (prepended).
     *
     * Sets {@link $registered} to `true` on self autoload register.
     *
     * No-op if self already registered (determined by {@link isRegistered()})
     * as an autoloader.
     *
     * Called automatically from {@link addPsr4()} and {@link addClassMap()}
     * if self not already registered (determined by {@link isRegistered()})
     * as an autoloader.
     */
    public static function register()
    {
        if (static::isRegistered()) {
            return;
        }

        spl_autoload_register(array(__CLASS__, 'loadClass'), true, true);
        static::$registered = true;
    }

    /**
     * Add PSR-4 mapping.
     *
     * If self not already registered (determined by {@link isRegistered()}),
     * registers by calling {@link register()}.
     *
     * Elements added to {@link $psr4} are:
     * ```php
     * array($prefix, $path)
     * ```
     *
     * @param string $prefix  Namespace prefix (automatically suffixed with `\`).
     * @param string $path    Base path/directory (automatically suffixed with `DIRECTORY_SEPARATOR`).
     * @param bool   $prepend Whether or not to prepend to PSR-4 mapping stack.
     *
     * @see http://www.php-fig.org/psr/psr-4/
     */
    public static function addPsr4($prefix, $path, $prepend = false)
    {
        if (empty($prefix)) {
            throw new \InvalidArgumentException('Prefix must not be empty (i.e. no failover paths).');
        }

        // If not registered, register.
        if (!static::isRegistered()) {
            static::register();
        }

        $prefix = trim($prefix, '\\').'\\';
        $path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        if ($prepend) {
            array_unshift(static::$psr4, array($prefix, $path));
        } else {
            static::$psr4[] = array($prefix, $path);
        }
    }

    /**
     * Returns protected PSR-4 mappings.
     *
     * @return array
     */
    public static function getPsr4()
    {
        return static::$psr4;
    }

    /**
     *
     */
    public static function addClassMap(array $classMap, $path)
    {
        // If not registered, register.
        if (!static::isRegistered()) {
            static::register();
        }
        $path = rtrim($path, DIRECTORY_SEPARATOR);
        if (isset(static::$classMap[$path])) {
            static::$classMap[$path] = array_merge($classMap, static::$classMap[$path]);
        } else {
            static::$classMap[$path] = $classMap;
        }
    }

    /**
     * Returns protected class mappings.
     *
     * @return array
     */
    public static function getClassMap()
    {
        return static::$classMap;
    }

    /**
     * Requires other autoloader dependency files.
     *
     * Loops through all $dependencies.  If a dependency is required,
     * it is always loaded.  If a dependency  is not required, it is
     * only loaded if the dependency autoloader file exists.
     *
     * Example:
     *
     * ```php
     * \Fedora\Autoloader::dependencies(array(
     *     // Required dependency so always load.
     *     '/usr/share/php/Foo/autoload.php' => true,
     *     // Optional dependency so only load if it exists.
     *     '/usr/share/php/Bar/autoload.php' => false,
     * ));
     * ```
     *
     * @param array $dependencies Autoloader dependency files.
     *     Keys: Dependency autoloader files.
     *     Values: Whether dependcy autoloader file is required or not.
     *
     */
    public static function dependencies(array $dependencies)
    {
        foreach ($dependencies as $dependency => $required) {
            if ($required || file_exists($dependency)) {
                requireFile($dependency);
            }
        }
    }

    /**
     * Loads a class' file.
     *
     * This is the self function registered as an autoload handler.
     */
    public static function loadClass($class)
    {
        if ($file = static::findFile($class)) {
            includeFile($file);
        }
    }

    /**
     * Finds a class' file.
     *
     * Checks for a classmap and then loops through PSR-4 mappings.
     */
    public static function findFile($class)
    {
        $lower = strtolower($class);
        foreach (static::$classMap as $dir => $classmap) {
            if (isset($classmap[$lower]) && file_exists($dir . $classmap[$lower])) {
                return $dir . $classmap[$lower];
            }
        }
        foreach (static::$psr4 as list($prefix, $path)) {
            if (0 === strpos($class, $prefix)) {
                $classWithoutPrefix = substr($class, strlen($prefix));
                $file = $path.str_replace('\\', DIRECTORY_SEPARATOR, $classWithoutPrefix).'.php';

                if (file_exists($file)) {
                    return $file;
                }
            }
        }
    }
}

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
