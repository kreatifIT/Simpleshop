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

        $brut_prices[22] += $Order->getValue('shipping_costs');

        if ($name == 'manual_discount') {
            $discount = $this->getValue('discount_value');
            $discount = $discount - $this->applyToNetPrices($discount, $brut_prices);

            $this->setValue('value', $discount);
        }
        else {
            if ($this->getValue('free_shipping')) {
                $Order->setValue('shipping_costs', 0);
                $discount = 0;
            }
            elseif ($this->getValue('discount_value') > 0) {
                $discount = $this->getValue('discount_value');
            }
            elseif ($this->getValue('discount_percent') > 0) {
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

    protected function applyToNetPrices($_discount, &$brut_prices)
    {
        // sort by tax percent
        krsort($brut_prices);
        $brut_discount = 0;

        foreach ($brut_prices as $tax => &$brut_price) {
            $net_price     = $brut_price + ($brut_price / 100 * $tax);
            $brut_discount += $_discount / (100 + $tax) * $tax;
            $brut_price    = $this->calcPriceAndDiff($net_price, $_discount) / (100 + $tax) * 100;

            if ($_discount <= 0) {
                break;
            }
        }
        return $brut_discount - $_discount;
    }

    protected function calcPriceAndDiff($price, &$diff)
    {
        if ($price < $diff) {
            $diff  = $diff - $price;
            $price = 0;
        }
        else {
            $price -= $diff;
            $diff  = 0;
        }
        return $price;
    }
}