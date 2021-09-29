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
        if (FragmentConfig::getValue('cart.use_discount_groups')) {
            $Order = $Ep->getParam('Order');
            list($promotions, $nextPromo) = self::getValidPromotions($Order);
            $promotions = array_merge($Ep->getSubject(), $promotions);
            $Ep->setSubject($promotions);
        }
    }

    public static function getValidPromotions(Order $order)
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
            }
            else if ($target == 'cart_value') {
                if ($order->getSubtotal(!$customer->isB2B()) >= $discount->getValue('price')) {
                    $promotions['discount_' . $discount->getValue('id')] = $discount;
                }
            }
            if (count($promotions) && !$applyAll) {
                break; // discount found - stop here
            }
        }
        $discounts     = array_reverse($discounts->toArray());
        $nextPromotion = current(array_slice($discounts, count($promotions), 1));

        return [$promotions, $nextPromotion];
    }

    public static function ext_getUpsellingPromotion(\rex_extension_point $ep)
    {
        if (FragmentConfig::getValue('cart.use_discount_groups')) {
            $promotions = $ep->getSubject();
            $order      = $ep->getParam('Order');
            $customer   = $order->getCustomerData();
            list($_, $nextPromo) = self::getValidPromotions($order);

            if ($nextPromo) {
                $target = $nextPromo->getValue('target');

                if ($target == 'order_quantities') {
                    $amountDiff = $nextPromo->getValue('amount') - $order->getValue('quantity');

                    if (!isset($promotions['order_quantities']) || $amountDiff < $promotions['order_quantities']['diff']) {
                        $wildcard = \Wildcard::get('action.add_product_amount_to_get_promotion');
                        $message  = strtr($wildcard, [
                            '{{AMOUNT}}' => "<strong>{$amountDiff}</strong>",
                            '{{NAME}}'   => $nextPromo->getName(),
                        ]);

                        $promotions['order_quantities'] = [
                            'diff'    => $amountDiff,
                            'message' => $message,
                        ];
                    }
                }
                else if ($target == 'cart_value') {
                    $priceDiff = $nextPromo->getValue('price') - $order->getSubtotal(!$customer->isB2B());

                    if (!isset($promotions['cart_value']) || $priceDiff < $promotions['cart_value']['diff']) {
                        $wildcard = \Wildcard::get('action.add_product_price_to_get_promotion');
                        $message  = strtr($wildcard, [
                            '{{PRICE}}' => "<strong>". format_price($priceDiff) ." €</strong>",
                            '{{NAME}}'  => $nextPromo->getName(),
                        ]);

                        $promotions['cart_value'] = [
                            'diff'    => $priceDiff,
                            'message' => $message,
                        ];
                    }
                }
                $ep->setSubject($promotions);
            }
        }
    }

    public static function ext__processSettings(\rex_extension_point $ep)
    {
        $sql    = \rex_sql::factory();
        $option = Settings::getValue('use_discount_groups');
        $yTable = \rex_yform_manager_table::get(DiscountGroup::TABLE);

        $sql->setTable('rex_yform_table');
        $sql->setValue('hidden', (int) !$option);
        $sql->setWhere(['table_name' => DiscountGroup::TABLE]);
        $sql->update();

        \rex_yform_manager_table_api::generateTableAndFields($yTable);
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