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

class DiscountGroup extends Model
{
    const TABLE = 'rex_shop_discount_group';


    public static function ext_calculateDocument($params)
    {
        $Order          = $params->getSubject();
        $order_total    = $Order->getValue('total');
        $order_quantity = $Order->getValue('quanity');
        $promotions     = $Order->getValue('promotions');
        $discounts      = self::query()->where('status', 1)->find();

        foreach ($discounts as $discount)
        {
            $price  = $discount->getValue('price');
            $amount = $discount->getValue('amount');

            if ($order_total > $price || $order_quantity > $amount)
            {
                $discount_id = $discount->getValue('id');
                $_this       = self::get($discount_id)->applyToOrder($Order);

                $promotions['discount_' . $discount_id] = $_this;
                $Order->setValue('promotions', $promotions);
                break; // discount found - stop here
            }
        }
        return $Order;
    }

    public function applyToOrder($Order)
    {
        if ($this->getValue('free_shipping'))
        {
            $Order->setValue('shipping_costs', 0);
        }
        else if ($this->getValue('discount_value') > 0)
        {
            $_discount = $this->getValue('discount_value');
            $Order->setValue('total', $Order->getValue('total') - $_discount);
            $this->setValue('discount', $_discount);
        }
        else if ($this->getValue('discount_percent') > 0)
        {
            $subtotal = $Order->getValue('subtotal');
            $_discount = $subtotal / 100 * $this->getValue('discount_percent');
            $Order->setValue('total', $Order->getValue('total') - $_discount);
            $this->setValue('discount', $_discount);
        }
        $Order->setValue('discount', $Order->getValue('discount') + $_discount);
        
        return $this;
    }
}