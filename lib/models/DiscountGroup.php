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
            $Order    = $Ep->getParam('Order');
            $products = $Ep->getParam('products');

            $Settings  = \rex::getConfig('simpleshop.Settings');
            $apply_all = from_array($Settings, 'discounts_are_accumulable', 0);
            $discounts = self::query()->where('status', 1)->orderBy('prio')->find();
            $Customer  = $Order->getValue('customer_data');
            $Cctype    = $Customer->getValue('ctype');

            foreach ($discounts as $discount) {
                $price  = $discount->getValue('price');
                $amount = $discount->getValue('amount');
                $Dctype = $discount->getValue('ctype', false, 'all');

                if ($Cctype == '' || $Dctype == 'all' || $Cctype == $Dctype) {
                    if ($amount) {
                        foreach ($products as $product) {
                            if ($product->getValue('discount') == '') {
                                if ($product->getValue('cart_quantity') >= $amount) {
                                    if ($discount->getValue('free_shipping')) {
                                        $promotions['discount_' . $discount->getValue('id')] = $discount;
                                    }
                                    else {
                                        $product->setValue('discount', $discount);
                                    }
                                }
                            }
                        }
                    }
                    elseif ($price) {
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