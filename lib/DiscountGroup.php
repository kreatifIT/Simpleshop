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
        $Order      = $Ep->getParam('Order');

        if (parent::isRegistered(self::TABLE)) {
            $Settings  = \rex::getConfig('simpleshop.Settings');
            $apply_all = from_array($Settings, 'discounts_are_accumulable', 0);
            $discounts = self::query()->where('status', 1)->orderBy('prio')->find();

            foreach ($discounts as $discount) {
                $price  = $discount->getValue('price');
                $amount = $discount->getValue('amount');

                if (($price && $Order->getValue('total') >= $price) || ($amount && $Order->getValue('quantity') >= $amount)) {
                    $promotions['discount_' . $discount->getValue('id')] = $discount;

                    if (!$apply_all) {
                        break; // discount found - stop here
                    }
                }
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
                $errors = '###shop.error.discountgroup_not_applyable_anymore###';
                break;
            default:
                $errors = $this->getMessage();
                break;
        }
        return $errors;
    }
}