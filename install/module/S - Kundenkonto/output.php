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

use FriendsOfREDAXO\Simpleshop;


$page     = rex_get('ctrl', 'string', $Settings['membera_area_contents'][0]);
$Settings = \rex::getConfig('simpleshop.Settings');

$Controller = Simpleshop\AccountController::execute([
    'controller' => $page,
]);
echo $Controller->parse();