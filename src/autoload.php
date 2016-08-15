<?php
/**
 * This file is part of the Fedora Autoloader package.
 *
 * (c) Shawn Iwinski <shawn@iwin.ski> and Remi Collet <remi@fedoraproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once __DIR__.'/Autoload.php';
require_once __DIR__.'/functions.php';

\Fedora\Autoloader\Autoload::addPsr4('Fedora\\Autoloader\\', __DIR__);
