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

use Kreatif\Model\Country;
use Sprog\Wildcard;


class DefaultShipping extends ShippingAbstract
{
    const NAME = 'label.shipping_default';

    protected $tax_percentage = 22;

    public function getName()
    {
        if ($this->name == '') {
            $this->name = Wildcard::get(self::NAME);
        }

        return parent::getName();
    }

    protected function calculatePrice(Order $Order)
    {
        $customer  = $Order->getCustomerData();
        $countryId = $this->getValue('country_id');
        $isCompany = $customer ? $customer->isB2B() : false;
        $total     = $Order->getSubtotal(!$isCompany);
        $Settings  = \rex::getConfig('simpleshop.DefaultShipping.Settings');

        if (!$countryId) {
            $Address   = $Order->getShippingAddress();
            $countryId = $Address ? $Address->getValue('country') : null;
        }
        $Country = $countryId ? Country::get($countryId) : null;

        if ($Country && isset($Settings['costs'][$Country->getId()])) {
            $cost = 0;

            foreach ($Settings['costs'][$Country->getId()] as $value => $_cost) {
                if ($total >= $value) {
                    $cost = $_cost;
                    break;
                }
            }
            $this->price = (float) $cost;
        }
        else {
            $freeShipping = (float) $Settings['general_free_shipping'];

            if ($freeShipping > 0 && $total >= $freeShipping) {
                $this->price = 0;
            }
            else {
                $this->price = (float) ($Settings['general_costs'] ?: 0);
            }
        }
    }

    public function getPrice(Order $Order, $products = null)
    {
        $this->calculatePrice($Order);

        return parent::getPrice($Order, $products);
    }

    public function getNetPrice(Order $Order, $products = null)
    {
        $this->calculatePrice($Order);

        return parent::getNetPrice($Order, $products);
    }

    public function getGrossPrice(Order $Order, $products = null)
    {
        $this->calculatePrice($Order);

        return parent::getGrossPrice($Order, $products);
    }

    public function applyToOrder() {}

    public static function ext_getUpsellingPromotion(\rex_extension_point $ep)
    {
        $promotions = $ep->getSubject();
        $order      = $ep->getParam('Order');
        $countryId  = $ep->getParam('country_id');
        $Country    = $countryId ? Country::get($countryId) : null;
        $Country    = $Country && $Country->isOnline() ? $Country : null;
        $Settings   = \rex::getConfig('simpleshop.DefaultShipping.Settings');
        $customer   = $order->getCustomerData();

        $priceDiff = 0;
        $subTotal  = $order->getSubtotal(!$customer->isB2B());

        if ($Country && isset($Settings['costs'][$Country->getId()])) {
            if (current($Settings['costs'][$Country->getId()]) == 0) {
                $costs = (float) key($Settings['costs'][$Country->getId()]);

                if ($subTotal < $costs) {
                    $priceDiff = $costs - $subTotal;
                }
            }
        }
        else {
            $freeShipping = (float) $Settings['general_free_shipping'];

            if ($freeShipping > 0 && $subTotal < $freeShipping) {
                $priceDiff = $freeShipping - $subTotal;
            }
        }

        if ($priceDiff > 0 && (!isset($promotions['free_shipping']) || $priceDiff < $promotions['free_shipping']['price_diff'])) {
            $wildcard = \Wildcard::get('action.add_product_price_to_get_promotion');
            $message  = strtr($wildcard, [
                '{{PRICE}}' => "<strong>{$priceDiff} €</strong>",
                '{{NAME}}'  => '###label.free_shipping###',
            ]);

            $promotions['free_shipping'] = [
                'price_diff' => $priceDiff,
                'message'    => $message,
            ];
        }
        $ep->setSubject($promotions);
    }

    public static function ext_applyDiscounts(\rex_extension_point $ep)
    {
        $promotions = $ep->getSubject();
        $order      = $ep->getParam('Order');
        $products   = $ep->getParam('products');
        $Address    = $order->getShippingAddress();
        $countryId  = $ep->getParam('country_id', $Address ? $Address->getValue('country') : null);

        $_this = self::create();
        $_this->setValue('country_id', $countryId);

        if ($_this->getPrice($order, $products) == 0) {
            $_this->name = '###label.free_shipping###';
            $promotions['free_shipping'] = $_this;
            $ep->setSubject($promotions);
        }
    }
}