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
    public function applyToOrder($Order, &$gross_prices, $name = '')
    {
        if (!$Order) {
            return false;
        }
        $discount = 0;

        if ($name == 'manual_discount') {
            $discount = $this->getValue('discount_value');
        } else if ($this->getValue('action') == 'percent_discount') {
            $shippingFaktor = [];
            if ($Order) {
                $shipping = $Order->getValue('shipping');
                if ($shipping) {
                    $shippingCost = $shipping->getPrice($Order);

                    $shippingFaktor = [
                        'percent' => $shipping->getTaxPercentage(),
                        'value'   => $shippingCost,
                    ];

                    $gross_prices[$shipping->getTaxPercentage()] -= $shippingCost;
                }
            }
            $discount = array_sum($gross_prices) / 100 * $this->getValue('discount_percent');

            if (count($shippingFaktor)) {
                $gross_prices[$shippingFaktor['percent']] += $shippingFaktor['value'];
            }
        } else if ($this->getValue('action') == 'fixed_discount') {
            $discount = $this->getValue('discount_value');
        } else if ($this->getValue('action') == 'free_shipping') {
            $Order->setValue('shipping_costs', 0);
        } else if ($this->getValue('action') == 'coupon_code') {
        }

        if ($discount > 0) {
            $discount = $discount - $this->applyToGrossPrices($discount, $gross_prices);
        }
        $this->setValue('value', $discount);

        return $discount;
    }

    public function applyToCart(&$brut_prices, $Order = null)
    {
        $discount = 0;

        if ($this->getValue('free_shipping')) {
            // nothing to do?
            $discount = 0;
        } else if ($this->getValue('discount_value') > 0) {
            $discount = $this->getValue('discount_value');
        } else if ($this->getValue('discount_percent') > 0) {
            $discount = array_sum($brut_prices) / 100 * $this->getValue('discount_percent');
        }

        if ($discount > 0) {
            $discount = $discount - $this->applyToGrossPrices($discount, $brut_prices);
        }
        $this->setValue('value', $discount);

        return $discount;
    }

    protected function applyToGrossPrices($_discount, &$gross_prices)
    {
        // sort by tax percent
        krsort($gross_prices);

        foreach ($gross_prices as $tax => &$gross_price) {
            $gross_price = $this->calcPriceAndDiff($gross_price, $_discount);

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