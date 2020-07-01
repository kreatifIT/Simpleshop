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

    protected function calculatePrice(Order $Order, $products = null)
    {
        $total     = 0;
        $Address   = $Order->getShippingAddress();
        $countryId = $Address ? $Address->getValue('country') : null;
        $isTaxFree = $Address ? $Address->isTaxFree() : false;
        $Country   = $countryId ? Country::get($countryId) : null;
        $Settings  = \rex::getConfig('simpleshop.DefaultShipping.Settings');
        $products  = $products ?: $Order->getProducts();

        foreach ($products as $product) {
            $total += $product->getPrice(!$isTaxFree);
        }

        if ($Country && isset($Settings['costs'][$Country->getId()])) {
            $cost = 0;

            foreach ($Settings['costs'][$Country->getId()] as $value => $_cost) {
                if ($total >= $value) {
                    $cost = $_cost;
                    break;
                }
            }
            $this->price = (float)$cost;
        } else {
            $freeShipping = (float)$Settings['general_free_shipping'];

            if ($freeShipping > 0 && $total >= $freeShipping) {
                $this->price = 0;
            } else {
                $this->price = (float)($Settings['general_costs'] ?: 0);
            }
        }
    }

    public function getPrice(Order $Order, $products = null)
    {
        $this->calculatePrice($Order, $products);
        return parent::getPrice($Order, $products);
    }

    public function getNetPrice(Order $Order, $products = null)
    {
        $this->calculatePrice($Order, $products);
        return parent::getNetPrice($Order, $products);
    }

    public function getGrossPrice(Order $Order, $products = null)
    {
        $this->calculatePrice($Order, $products);
        return parent::getGrossPrice($Order, $products);
    }
}