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
     * @var bool Whether self is registered as an autoloader
     */
    protected static $registered = false;

    /**
     * @var array Class map. See addClassMap() for description of elements
     */
    protected static $classMap = array();

    /**
     * @var array PSR-4 mapping stack. See addPsr4() for description of elements
     */
    protected static $psr4 = array();

    /**
     * @var array PSR-0 mapping stack. See addPsr0() for description of elements
     */
    protected static $psr0 = array();

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
     * Add PSR-0 mapping.
     *
     * If self not already registered (determined by {@link isRegistered()}),
     * registers by calling {@link register()}.
     *
     * Elements added to {@link $psr0} are:
     * ```php
     * array($prefix, $path)
     * ```
     *
     * @param string $prefix  Class or Namespace prefix (no `\` added)
     * @param string $path    Base path/directory (automatically suffixed with `DIRECTORY_SEPARATOR`)
     * @param bool   $prepend Whether or not to prepend to PSR-0 mapping stack
     *
     * @see http://www.php-fig.org/psr/psr-0/
     */
    public static function addPsr0($prefix, $path, $prepend = false)
    {
        // If not registered, register.
        if (!static::isRegistered()) {
            static::register();
        }

        $path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        if ($prepend) {
            array_unshift(static::$psr0, array($prefix, $path));
        } else {
            static::$psr0[] = array($prefix, $path);
        }
    }

    /**
     * Returns protected PSR-0 mappings.
     *
     * @return array
     */
    public static function getPsr0()
    {
        return static::$psr0;
    }

    /**
     * Add every dir in the include_path as a PSR-0 tree.
     */
    public static function addIncludePath()
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());
        foreach ($paths as $path) {
            if ($path == '.') {
                continue;
            }
            self::addPsr0('', $path);
        }
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
     * @param string $prefix  Namespace prefix (automatically suffixed with `\`)
     * @param string $path    Base path/directory (automatically suffixed with `DIRECTORY_SEPARATOR`)
     * @param bool   $prepend Whether or not to prepend to PSR-4 mapping stack
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
     * Loads a class' file.
     *
     * This is the self function registered as an autoload handler.
     *
     * @return bool
     */
    public static function loadClass($class)
    {
        if ($file = static::findFileForClass($class)) {
            includeFile($file);
            return true;
        }
        return false;
    }

    /**
     * Finds a class' file.
     *
     * Checks for a classmap and then loops through PSR-4 mappings.
     */
    public static function findFileForClass($class)
    {
        $class = ltrim($class, '\\');
        $lower = strtolower($class);

        // Classmap
        foreach (static::$classMap as $dir => $classmap) {
            if (isset($classmap[$lower]) && file_exists($dir.$classmap[$lower])) {
                return $dir.$classmap[$lower];
            }
        }

        // PSR-4

        // NOTE: Cannot use `foreach (static::$psr4 as list($prefix, $path))`
        //       for PHP < 5.5 compatibility.
        foreach (static::$psr4 as $psr4) {
            list($prefix, $path) = $psr4;

            if (0 === strpos($class, $prefix)) {
                $classWithoutPrefix = substr($class, strlen($prefix));
                $file = $path.str_replace('\\', DIRECTORY_SEPARATOR, $classWithoutPrefix).'.php';

                if (file_exists($file)) {
                    return $file;
                }
            }
        }

        // PSR-0
        if (count(static::$psr0)) {
            $pos = strrpos($class, '\\');
            $file = $namespace = '';
            if ($pos) {
                $namespace = substr($class, 0, $pos + 1);
                $class = substr($class, $pos + 1);
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
            }
            $file .= str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';

            // NOTE: Cannot use `foreach (static::$psr0 as list($prefix, $path))`
            //       for PHP < 5.5 compatibility.
            foreach (static::$psr0 as $psr0) {
                list($prefix, $path) = $psr0;
                if (empty($prefix) || 0 === strpos($namespace.$class, $prefix)) {
                    if (file_exists($path.$file)) {
                        return $path.$file;
                    }
                }
            }
        }
    }
}
