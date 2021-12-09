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

use Kreatif\Model;


abstract class Discount extends Model
{
    public function applyToOrder($Order, &$netPrices, $name = '')
    {
        if (!$Order) {
            return false;
        }
        $discount        = 0;
        $applyToShipping = !(int)Utils::getSetting('not_apply_discounts_to_shipping');

        if ($name == 'manual_discount') {
            $discount = $this->getValue('discount_value');
        } elseif ($this->getValue('action') == 'percent_discount') {
            $shippingFaktor = [];

            if ($Order) {
                $shipping = $Order->getValue('shipping');

                if ($applyToShipping && $shipping) {
                    $shippingCost = $shipping->getPrice($Order);
                    $_percent     = $shipping->getTaxPercentage();
                    $_costs       = $shippingCost / (100 + $shipping->getTaxPercentage()) * 100;

                    $shippingFaktor = [
                        'percent' => $_percent,
                        'value'   => $_costs,
                    ];

                    $netPrices[$shipping->getTaxPercentage()] -= $_costs;
                }
            }
            foreach ($netPrices as $tax => $netPrice) {
                $discount += $netPrice / 100 * $this->getValue('discount_percent') * ($tax / 100 + 1);
            }

            if ($applyToShipping && count($shippingFaktor)) {
                $netPrices[$shippingFaktor['percent']] += $shippingFaktor['value'];
            }
        } elseif ($this->getValue('action') == 'fixed_discount') {
            $discount = $this->getValue('discount_value');
        } elseif ($this->getValue('action') == 'free_shipping') {
            $Order->setValue('shipping_costs', 0);
        } elseif ($this->getValue('action') == 'coupon_code') {
        }

        if ($discount > 0) {
            $discount = $discount - $this->applyToNetPrices($discount, $netPrices);
        }
        $this->setValue('value', $discount);

        return $discount;
    }

    protected function applyToNetPrices($_discount, &$netPrices)
    {
        // sort by tax percent
        krsort($netPrices);

        foreach ($netPrices as $tax => &$netPrice) {
            $netPrice = $netPrice * ($tax / 100 + 1);
            $netPrice = $this->calcPriceAndDiff($netPrice,  $_discount);
            $netPrice = $netPrice / ($tax / 100 + 1);

            if ($_discount <= 0) {
                break;
            }
        }
        return $_discount;
    }

    protected function calcPriceAndDiff($price, &$diff)
    {
        if ($price < $diff) {
            $diff  = $diff - $price;
            $price = 0;
        } else {
            $price -= $diff;
            $diff  = 0;
        }
        return $price;
    }
}