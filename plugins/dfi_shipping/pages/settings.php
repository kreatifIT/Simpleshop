<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

$fragment = new \rex_fragment();
$fragment->setVar('key', 'simpleshop.DfiShipping.Settings');
$fragment->setVar('fragment_path', 'simpleshop/backend/dfi_shipping_settings.php');
echo $fragment->parse('simpleshop/backend/settings_wrapper.php');
