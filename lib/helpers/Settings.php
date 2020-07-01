<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 20.05.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;


class Settings
{

    public static function init()
    {
        \rex_login::startSession();
        $beUser = \rex::getUser();
        $mpdf   = \rex_addon::get('kreatif-mpdf');

        if (rex_get('action', 'string') == 'logout') {
            Customer::logout();

            $login = new \rex_backend_login();
            $login->setLogout(true);
            $login->checkLogin();

            \rex_response::sendCacheControl();
            \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
            \rex_response::sendRedirect(rex_getUrl(null, null, ['ts' => time()]));
        }
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
        if ($mpdf->isAvailable()) {
            $addon = \rex_addon::get('simpleshop');
            FragmentConfig::$data['checkout']['generate_pdf'] = true;
            \Kreatif\Mpdf\Mpdf::addCSSPath($addon->getPath('assets/scss/pdf_styles.scss'));
        }

        $tableIsHidden = \rex_yform_manager_table::get(Coupon::TABLE)->isHidden();
        FragmentConfig::$data['cart']['has_coupons'] = !$tableIsHidden;
        FragmentConfig::$data['checkout']['has_coupons'] = !$tableIsHidden;

        $tableIsHidden = \rex_yform_manager_table::get(DiscountGroup::TABLE)->isHidden();
        FragmentConfig::$data['cart']['use_discount_groups'] = !$tableIsHidden;
    }

    public static function getValue($key, $plugin = '')
    {
        $plugin   = $plugin == '' ? '' : '.' . $plugin;
        $settings = \rex::getConfig('simpleshop.Settings' . $plugin);

        return $settings[$key];
    }

    public static function getArticle($key)
    {
        $linklist = self::getValue('linklist');
        $article  = isset($linklist[$key]) ? \rex_article::get($linklist[$key]) : null;

        if ($article && !$article->isOnline()) {
            $article = null;
        }
        return $article;
    }
}