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

    public static function ext_calculateDocument($params)
    {
        $Order = $params->getSubject();

        if (parent::isRegistered(self::TABLE)) {
            $Settings       = \rex::getConfig('simpleshop.Settings');
            $apply_all      = from_array($Settings, 'discounts_are_accumulable', 0);
            $order_total    = $Order->getValue('total');
            $order_quantity = $Order->getValue('quantity');
            $discounts      = self::query()->where('status', 1)->orderBy('prio')->find();

            foreach ($discounts as $discount) {
                $price  = $discount->getValue('price');
                $amount = $discount->getValue('amount');

                if (($price && $order_total >= $price) || ($amount && $order_quantity >= $amount)) {
                    $discount_id                            = $discount->getValue('id');
                    $promotions                             = $Order->getValue('promotions');
                    $promotions['discount_' . $discount_id] = $discount;
                    $Order->setValue('promotions', $promotions);

                    if (!$apply_all) {
                        break; // discount found - stop here
                    }
                }
            }
        }
        return $Order;
    }
}

class DiscountGroupException extends \Exception
{
    public function getLabelByCode()
    {
        switch ($this->getCode()) {
            case 1:
                $errors = '###shop.error.discountgroup_not_applyable_anymore###';
                break;
            default:
                $errors = $this->getMessage();
                break;
        }
        return $errors;
    }
}