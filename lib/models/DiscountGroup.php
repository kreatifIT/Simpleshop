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

        if (parent::isRegistered(self::TABLE)) {
            $Order      = $Ep->getParam('Order');
            $promotions = self::getValidPromotions($Ep->getParam('products'), $Order->getValue('customer_data'));
        }
        return $promotions;
    }

    public static function getValidPromotions($products, $Customer = null)
    {
        $promotions = [];
        $Settings   = \rex::getConfig('simpleshop.Settings');
        $apply_all  = from_array($Settings, 'discounts_are_accumulable', 0);
        $discounts  = self::query()->where('status', 1)->orderBy('prio')->find();
        $address    = $Customer ? $Customer->getAddress() : null;
        $Cctype     = $address ? $address->getValue('ctype') : 'person';

        foreach ($discounts as $discount) {
            $dfound = false;
            $price  = $discount->getValue('price');
            $amount = $discount->getValue('amount');
            $Dctype = $discount->getValue('ctype', false, 'all');

            if ($Cctype == '' || $Dctype == 'all' || $Cctype == $Dctype) {
                if ($amount) {
                    foreach ($products as $product) {
                        if ($product->getValue('discount') == '' && $product->getValue('cart_quantity') >= $amount) {
                            $dfound = true;

                            if ($discount->getValue('free_shipping')) {
                                $promotions['discount_' . $discount->getValue('id')] = $discount;
                            }
                            else {
                                $product->setValue('discount', $discount);
                            }
                        }
                    }
                    if ($dfound && !$apply_all) {
                        break; // discount found - stop here
                    }
                }
                elseif ($price) {
                    $cartTotal = 0;

                    foreach ($products as $product) {
                        $cartTotal += $product->getPrice() * $product->getValue('cart_quantity');
                    }
                    if ($cartTotal >= $price) {
                        $promotions['discount_' . $discount->getValue('id')] = $discount;

                        if (!$apply_all) {
                            break; // discount found - stop here
                        }
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
                $errors = '###simpleshop.error.discountgroup_not_applyable_anymore###';
                break;
            default:
                $errors = $this->getMessage();
                break;
        }
        return $errors;
    }
}