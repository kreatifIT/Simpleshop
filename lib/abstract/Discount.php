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

abstract class Discount extends Model
{
    public function applyToOrder($Order)
    {
        if (!$Order) {
            return false;
        }
        if ($this->getValue('free_shipping')) {
            $Order->setValue('shipping_costs', 0);
            $Order->setValue('initial_shipping_costs', 0);
        }
        else if ($this->getValue('discount_value') > 0 || $this->getValue('discount_percent') > 0) {
            $net_prices = $Order->getValue('net_prices');
            $shipping   = $Order->getValue('shipping_costs');
            $_discount  = $this->getValue('discount_value') ?: ($this->getValue('discount_percent') ? array_sum($net_prices) / 100 * $this->getValue('discount_percent') : 0);
            $discount   = $_discount - $this->applyToNetPrices($_discount, $net_prices, $shipping);

            $Order->setValue('net_prices', $net_prices);
            $Order->setValue('shipping_costs', $shipping);
            $Order->setValue('discount', $Order->getValue('discount') + $discount);
        }
        return $this;
    }

    protected function applyToNetPrices($_discount, &$net_prices, &$shipping)
    {
        // sort by tax percent
        krsort($net_prices);

        if ($shipping) {
            $shipping = $this->calcPriceAndDiff($shipping, $_discount);
        }
        foreach ($net_prices as &$net_price) {
            $net_price = $this->calcPriceAndDiff($net_price, $_discount);

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
        }
        else {
            $price -= $diff;
            $diff = 0;
        }
        return $price;
    }
}