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
    public function applyToOrder($Order, &$gross_prices, $name = '')
    {
        if (!$Order) {
            return false;
        }
        $discount = 0;

        if ($name == 'manual_discount') {
            $discount = $this->getValue('discount_value');
        } else if ($this->getValue('free_shipping')) {
            $Order->setValue('shipping_costs', 0);
            $discount = 0;
        } else if ($this->getValue('discount_value') > 0) {
            $discount = $this->getValue('discount_value');
        } else if ($this->getValue('discount_percent') > 0) {
            $discount = array_sum($gross_prices) / 100 * $this->getValue('discount_percent');
        }

        if ($discount > 0) {
            [$discountValue, $netDiscounts, $taxDiscount] = $this->applyToGrossPrices($discount, $gross_prices);
            $discount = $discount - $discountValue;
        }
        $this->setValue('value', $discount);
        $this->setValue('net_values', $netDiscounts);
        $this->setValue('tax_value', $taxDiscount);

        return $discount;
    }

    public function applyToCart(&$brut_prices)
    {
        $discount = 0;
        $taxDiscount = 0;

        if ($this->getValue('free_shipping')) {
            // nothing to do?
            $discount = 0;
        } else if ($this->getValue('discount_value') > 0) {
            $discount = $this->getValue('discount_value');
        } else if ($this->getValue('discount_percent') > 0) {
            $discount = array_sum($brut_prices) / 100 * $this->getValue('discount_percent');
        }

        if ($discount > 0) {
            [$discountValue, $netDiscounts, $taxDiscount] = $this->applyToGrossPrices($discount, $brut_prices);
            $discount = $discount - $discountValue;
        }
        $this->setValue('value', $discount);
        $this->setValue('net_values', $netDiscounts);
        $this->setValue('tax_value', $taxDiscount);

        return $discount;
    }

    protected function applyToGrossPrices($_discount, &$gross_prices)
    {
        // sort by tax percent
        krsort($gross_prices);
        $taxDiscount = [];
        $netDiscounts = [];

        foreach ($gross_prices as $tax => &$gross_price) {
            $_grossPrice = $gross_price;
            $gross_price = $this->calcPriceAndDiff($gross_price, $_discount);
            $discountValue = $_grossPrice - $gross_price;
            $taxDiscount[$tax] = $discountValue / ($tax + 100) * $tax;
            $netDiscounts[$tax] = $discountValue / ($tax + 100) * 100;

            if ($_discount <= 0) {
                break;
            }
        }
        return [$_discount, $netDiscounts, $taxDiscount];
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