<?php

/**
 * This file is part of the Shop package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 12.10.16
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Category', 'ext_yform_data_delete']);
rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Feature', 'ext_yform_data_delete']);
rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Product', 'ext_yform_data_delete']);
rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Tax', 'ext_yform_data_delete']);
