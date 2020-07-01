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


\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Feature', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\FeatureValue', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Product', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Tax', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_DATA_DELETE', ['\FriendsOfREDAXO\Simpleshop\Order', 'ext_yform_data_delete']);
\rex_extension::register('YFORM_MANAGER_REX_INFO', ['\FriendsOfREDAXO\Simpleshop\Product', 'ext_tableManagerInfo']);
\rex_extension::register('kreatif.Model.queryCollection', ['\FriendsOfREDAXO\Simpleshop\Product', 'ext_queryCollection']);
\rex_extension::register('project.layoutBottom', ['\FriendsOfREDAXO\Simpleshop\CartController', 'ext_project_layoutBottom']);
\rex_extension::register('kreatif.setUrlObject', ['\FriendsOfREDAXO\Simpleshop\Product', 'ext_setUrlObject']);
\rex_extension::register('simpleshop.Order.completeOrder', ['\FriendsOfREDAXO\Simpleshop\Coupon', 'ext_completeOrder']);
\rex_extension::register('simpleshop.Checkout.orderComplete', ['\FriendsOfREDAXO\Simpleshop\Coupon', 'ext_beforeSendOrder']);
\rex_extension::register('simpleshop.Order.applyDiscounts', ['\FriendsOfREDAXO\Simpleshop\DiscountGroup', 'ext_applyDiscounts']);
\rex_extension::register('kreatif.Model.unprepareNEObject', ['\FriendsOfREDAXO\Simpleshop\Std', 'ext_unprepareNEObject']);


\rex_extension::register('PACKAGES_INCLUDED', function (\rex_extension_point $Ep) {
    if ($this->getConfig('installed')) {
        \rex_login::startSession();

        if (rex_get('action', 'string') == 'logout') {
            Customer::logout();

            $login = new \rex_backend_login();
            $login->setLogout(true);
            $login->checkLogin();

            \rex_response::sendCacheControl();
            \rex_response::sendRedirect(rex_getUrl(null));
        }

        $beUser = \rex::getUser();

        if ($beUser) {
            $Customer = Customer::getCurrentUser();

            if (!$Customer) {
                Customer::login($beUser->getEmail(), 'backend');
            }

            if (\rex::isBackend()) {
                \rex_view::setJsProperty('simpleshop', [
                    'ajax_url' => \rex_url::frontendController(),
                ]);
            }
        }

        $mpdf = \rex_addon::get('kreatif-mpdf');

        if ($mpdf->isAvailable()) {
            FragmentConfig::$data['checkout']['generate_pdf'] = true;
            \Kreatif\Mpdf\Mpdf::addCSSPath($this->getPath('assets/scss/pdf_styles.scss'));
        }
    }
    return $Ep->getSubject();
});

\rex_extension::register('yform/usability.getStatusColumnParams.options', function (\rex_extension_point $Ep) {
    $options = $Ep->getSubject();
    $table   = $Ep->getParam('table');

    if ($table == Order::TABLE) {
        $list = $Ep->getParam('list');

        if ($list && $list->getValue('ref_order_id')) {
            $options = ['CN' => $options['CN']];
        } else if ($list && count(Order::query()
                ->where('ref_order_id', $list->getValue('id'))
                ->find())
        ) {
            $options = ['CA' => $options['CA']];
        } else {
            unset($options['CN']);
        }
    }

    return $options;
});

\rex_extension::register('simpleshop.Settings.saved', function (\rex_extension_point $Ep) {
    Coupon::ext__processSettings($Ep);
});

\rex_view::setJsProperty('simpleshop', [
    'ajax_url' => \rex_url::frontendController(),
]);