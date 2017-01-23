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

namespace FriendsOfREDAXO\Simpleshop;

\rex_extension::register('REX_YFORM_SAVED', ['\FriendsOfREDAXO\Simpleshop\Order', 'ext_yform_saved']);

\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Category', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Feature', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\FeatureValue', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Product', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Tax', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Order', 'ext_yform_data_delete']);
\rex_extension::register('CACHE_DELETED', ['\FriendsOfREDAXO\Simpleshop\Utils', 'ext_register_tables']);

\rex_extension::register('simpleshop.Order.calculateDocument', ['\FriendsOfREDAXO\Simpleshop\DiscountGroup', 'ext_calculateDocument']);

// yform/usability extensions
\rex_extension::register('yform/usability.addStatusToggle', function ($params)
{
    $table   = $params->getParam('table');
    return $table->getTableName() != Order::TABLE;
});
\rex_extension::register('yform/usability.addDragNDropSort.filters', function ($params)
{
    $subject = $params->getSubject();
    $params  = $params->getParam('list_params');

    if ($params['params']['table']->getTableName() == Category::TABLE)
    {
        $_filter = rex_get('rex_yform_filter', 'array');

        if(isset($_filter['parent_id']))
        {
            $subject[] = 'parent_id='. (int) $_filter['parent_id'];
        }
        else
        {
            $subject[] = 'parent_id=0';
        }
    }
    return $subject;
});
