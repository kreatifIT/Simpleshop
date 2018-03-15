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

$values     = rex_var::toArray('REX_VALUE[1]');
$Controller = Simpleshop\AccountController::execute($values);
echo $Controller->parse();