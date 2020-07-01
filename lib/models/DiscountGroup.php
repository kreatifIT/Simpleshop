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


class DiscountGroup extends Discount
{
    const TABLE = 'rex_shop_discount_group';

    public static function ext_applyDiscounts(\rex_extension_point $Ep)
    {
        $promotions = $Ep->getSubject();
        $useThis    = FragmentConfig::getValue('cart.use_discount_groups');

        if ($useThis) {
            $Order      = $Ep->getParam('Order');
            $promotions = self::getValidPromotions($Ep->getParam('products'), $Order);
        }
        return $promotions;
    }

    public static function getValidPromotions($products, Order $order)
    {
        $promotions = [];
        $customer   = $order->getCustomerData();
        $applyAll   = Settings::getValue('discounts_are_accumulable', 'general');
        $Cctype     = $customer ? $customer->getCtype() : 'person';

        $query = self::query();
        $query->where('status', 1);
        $query->whereRaw('(
            (target = "order_quantities" AND amount > 0)
            OR (target = "cart_value" AND price > 0)
        )');
        $query->whereRaw('(
            ctype IS NULL 
            OR ctype = "" 
            OR ctype = "all" 
            OR ctype = :type
        )', ['type' => $Cctype]);
        $query->orderBy('target', 'desc');
        $query->orderBy('price', 'desc');
        $query->orderBy('amount', 'desc');
        $discounts = $query->find('status', 1);

        foreach ($discounts as $discount) {
            $target = $discount->getValue('target');

            if ($target == 'order_quantities') {
                if ($order->getValue('quantity') >= $discount->getValue('amount')) {
                    $promotions['discount_' . $discount->getValue('id')] = $discount;
                }
            } else if ($target == 'cart_value') {
                if ($order->getSubtotal() >= $discount->getValue('price')) {
                    $promotions['discount_' . $discount->getValue('id')] = $discount;
                }
            }
            if (count($promotions) && !$applyAll) {
                break; // discount found - stop here
            }
        }
        return $promotions;
    }
}

class DiscountGroupException extends \Exception
{
    public function getLabelByCode()
    {
        switch ($this->getCode()) {
            case 1:
                $errors = '###error.discountgroup_not_applyable_anymore###';
                break;
            default:
                $errors = $this->getMessage();
                break;
        }
        return $errors;
    }
}