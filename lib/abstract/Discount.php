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
    public function applyToOrder($Order, &$brut_prices, $name = '')
    {
        if (!$Order) {
            return false;
        }
        if ($name == 'manual_discount') {
            $discount = $this->getValue('discount_value');
            $discount = $discount - $this->applyToNetPrices($discount, $brut_prices);

            $this->setValue('value', $discount);
        } else {
            if ($this->getValue('free_shipping')) {
                $Order->setValue('shipping_costs', 0);
                $discount = 0;
            } else if ($this->getValue('discount_value') > 0) {
                $discount = $this->getValue('discount_value');
            } else if ($this->getValue('discount_percent') > 0) {
                $discount = array_sum($brut_prices) / 100 * $this->getValue('discount_percent');
            }
            $discount = $discount - $this->applyToGrossPrices($discount, $brut_prices);

            $this->setValue('value', $discount);
        }
        return $discount;
    }

    protected function applyToGrossPrices($_discount, &$brut_prices)
    {
        // sort by tax percent
        krsort($brut_prices);

        foreach ($brut_prices as &$brut_price) {
            $brut_price = $this->calcPriceAndDiff($brut_price, $_discount);

            if ($_discount <= 0) {
                break;
            }
        }
        return $_discount;
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