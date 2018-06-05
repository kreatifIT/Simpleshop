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

\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Category', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Feature', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\FeatureValue', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Product', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Tax', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Order', 'ext_yform_data_delete']);
\rex_extension::register('CACHE_DELETED', ['\FriendsOfREDAXO\Simpleshop\Utils', 'ext_register_tables']);

\rex_extension::register('simpleshop.Order.applyDiscounts', ['\FriendsOfREDAXO\Simpleshop\DiscountGroup', 'ext_applyDiscounts']);

\rex_extension::register('yform/usability.getStatusColumnParams.options', function (\rex_extension_point $Ep) {
    $options = $Ep->getSubject();
    $table   = $Ep->getParam('table');

    if ($table == Order::TABLE) {
        $list = $Ep->getParam('list');

        if ($list && $list->getValue('ref_order_id')) {
            $options = ['CN' => $options['CN']];
        }
        else if ($list && count(Order::query()->where('ref_order_id', $list->getValue('id'))->find())) {
            $options = ['CA' => $options['CA']];
        }
        else {
            unset($options['CN']);
        }
    }

    return $options;
});
\rex_extension::register('yform/usability.addDragNDropSort.filters', function (\rex_extension_point $Ep) {
    $subject = $Ep->getSubject();
    $params  = $Ep->getParam('list_params');

    if (is_object($params['params']['table']) && $params['params']['table']->getTableName() == Category::TABLE) {
        $_filter = rex_get('rex_yform_filter', 'array');

        if (isset($_filter['parent_id'])) {
            $subject[] = 'parent_id=' . (int) $_filter['parent_id'];
        }
        else {
            $subject[] = 'parent_id=0';
        }
    }

    return $subject;
});

\rex_extension::register('PACKAGES_INCLUDED', function () {
    $pdf_styles   = \rex::getProperty('mpdf-styles', []);
    $pdf_styles[] = $this->getPath('assets/pdf_styles.scss');

    \rex::setProperty('mpdf-styles', $pdf_styles);
});