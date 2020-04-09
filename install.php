<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 * @author jan.kristinus@yakamara.de
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

rex_dir::create(rex_path::addonData('simpleshop', 'log'), true);
rex_dir::create(rex_path::addonData('simpleshop', 'packing_lists'), true);


if (method_exists('\Sprog\Sync', 'ensureAddonWildcards')) {
    \Sprog\Sync::ensureAddonWildcards($this);
}

$installFiles = glob($this->getPath('install/inc/*.inc.php'));

foreach ($installFiles as $installFile) {
    include_once $installFile;
}

$this->setConfig('installed', true);
rex_delete_cache();
