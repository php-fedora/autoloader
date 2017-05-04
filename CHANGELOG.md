# CHANGELOG

## 1.0.0 (2017-05-04)

First stable release.  No changes since 1.0.0-rc1.  May the 4th be with you!

## 1.0.0-rc1 (2017-03-13)

* Avoid loading unneeded depency autoloader if one of the alternative
  versions is already loaded
  ([#12](https://github.com/php-fedora/autoloader/issues/12),
  [#13](https://github.com/php-fedora/autoloader/pull/13))
* Fix for recent phpunit versions
  ([#17](https://github.com/php-fedora/autoloader/pull/17))
* Allow non-full-path in dependencies list and rely on include_path
  ([#14](https://github.com/php-fedora/autoloader/pull/14))

## 0.2.1 (2016-10-28)

* Remove self-autoload constant
  ([#11](https://github.com/php-fedora/autoloader/pull/11))

## 0.2.0 (2016-10-26)

* Required dependencies raise an exception if not found or readable
  ([#7](https://github.com/php-fedora/autoloader/pull/7))
* phpab template updates
  ([#8](https://github.com/php-fedora/autoloader/pull/8))
* Refactor Dependencies::process() with exceptions
  ([#9](https://github.com/php-fedora/autoloader/pull/9))
* Refactor autoload.php with a constant conditional
  ([#10](https://github.com/php-fedora/autoloader/pull/10))

## 0.1.2 (2016-10-21)

* Fixed [#5](https://github.com/php-fedora/autoloader/issues/5):
  Handle namespaced classes with PSR-0
  ([#6](https://github.com/php-fedora/autoloader/pull/6))

## 0.1.1 (2016-10-19)

* Fix `phpab` template

## 0.1.0 (2016-10-19)

* Initial release
