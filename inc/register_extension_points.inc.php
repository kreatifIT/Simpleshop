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
\rex_extension::register('project.layoutBottom', ['\FriendsOfREDAXO\Simpleshop\CartController', 'ext_project_layoutBottom']);
\rex_extension::register('YFORM_DATASET_FORM_SETVALUEFIELD', ['\FriendsOfREDAXO\Simpleshop\Model', 'ext_setValueField']);
\rex_extension::register('YFORM_DATASET_FORM_SETVALIDATEFIELD', ['\FriendsOfREDAXO\Simpleshop\Model', 'ext_setValidateField']);


\rex_extension::register('PACKAGES_INCLUDED', function (\rex_extension_point $Ep) {
    if (rex_get('action', 'string') == 'logout') {
        Customer::logout();
    }
    return $Ep->getSubject();
});

\rex_extension::register('simpleshop.Order.applyDiscounts', ['\FriendsOfREDAXO\Simpleshop\DiscountGroup', 'ext_applyDiscounts']);

\rex_extension::register('yform/usability.addDragNDropSort.filters', function ($params) {
    $subject = $params->getSubject();
    $params  = $params->getParam('list_params');

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

\rex_view::setJsProperty('simpleshop', [
    'ajax_url'   => \rex_url::frontendController(),
    'loadingDiv' => '
        <div class="pjax-loading">
            <div class="spinner">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
                <div class="rect4"></div>
                <div class="rect5"></div>
            </div>
        </div>
    ',
]);