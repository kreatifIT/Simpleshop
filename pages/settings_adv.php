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

namespace FriendsOfREDAXO\Simpleshop;

$fragment = new \rex_fragment();
$fragment->setVar('Addon', $this);
$fragment->setVar('key', 'simpleshop.Settings');
$fragment->setVar('fragment_path', 'simpleshop/backend/settings_adv.php');
echo $fragment->parse('simpleshop/backend/settings_wrapper.php');
