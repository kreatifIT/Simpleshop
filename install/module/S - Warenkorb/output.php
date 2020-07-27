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

$values   = rex_var::toArray('REX_VALUE[1]');
$settings = rex_var::toArray('REX_VALUE[20]');

$fragment = new rex_fragment();
$fragment->setVar('values', $values, false);
$fragment->setVar('settings', $settings);
$fragment->setVar('module_id', 'REX_MODULE_ID');
$fragment->setVar('slice_id', 'REX_SLICE_ID');
$fragment->setVar('fragment_path', 'simpleshop/modules/cart/default.php');
echo $fragment->parse('kreatif/templates/default/module_wrapper.php');
