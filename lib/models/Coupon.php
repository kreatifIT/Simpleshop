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


use Kreatif\Yform;
use Sprog\Wildcard;


class Coupon extends Discount
{
    const TABLE = 'rex_shop_coupon';

    public static function redeem($code)
    {
        // save coupon to apply it also on page refresh
        Session::setCheckoutData('coupon_code', $code);

        if ($code == '') {
            return false;
        }
        $_this = self::getByCode($code);

        if (!$_this) {
            throw new CouponException('Coupon not exists', 1);
        }
        \rex_extension::register('simpleshop.Order.applyDiscounts', [$_this, 'ext_applyDiscounts']);

        return $_this;
    }

    public static function cloneCode($codeId, $amount = 1, $_data = [], $status = 'usable')
    {
        \rex_yform_manager_dataset::clearInstancePool();

        $result = [];
        $Coupon = $codeId ? self::get($codeId) : null;

        if (!$Coupon) {
            return false;
        }
        $data = $Coupon->getData();

        // cloning codes
        for ($i = 0; $i < $amount; $i++) {
            $clone = self::create();

            foreach ($data as $name => $value) {
                $clone->setValue($name, $value);
            }
            foreach ($_data as $_key => $_value) {
                $clone->setValue($_key, $_value);
            }
            $clone->setValue('orders', null);
            $clone->setValue('code', \rex_yform_value_coupon_code::getRandomCode());
            $clone->setValue('createdate', date('Y-m-d H:i:s'));
            $clone->setValue('status', $status);
            $clone->save();
            $clone->getData();
            $result[] = $clone;
        }
        return $result;
    }

    public static function createGiftcard($Order, $Product)
    {
        $order_id = $Order->getID();

        $_this = self::create();
        $_this->setValue('code', \rex_yform_value_coupon_code::getRandomCode());
        $_this->setValue('status', 'givenaway');
        $_this->setValue('discount_value', $Product->getPrice());
        $_this->setValue('prefix', 'OR');
        $_this->setValue('orders', $order_id);
        $_this->setValue('start_time', $Order->getValue('status') != 'OP' ? date('Y-m-d') : '9999-12-31');

        // set names
        $langs = \rex_clang::getAll(true);

        foreach ($langs as $lang) {
            $lang_id = $lang->getId();
            $_this->setValue('name_' . $lang_id, Wildcard::get('label.coupon', $lang_id) . ' #' . $order_id);
        }

        $_this->save();

        // set code to product
        $extras                = $Product->getValue('extras');
        $extras['coupon_code'] = $_this->prefix . '-' . $_this->code;
        $Product->setValue('extras', $extras);

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Coupon.createGiftcard.CREATED', $_this, ['Order' => $Order, 'Product' => $Product]));
    }

    public function applyToOrder($Order, &$brut_prices, $name = '')
    {
        return $this->apply('to-order', $Order, $brut_prices);
    }

    public function applyToCart(&$brut_prices)
    {
        return $this->apply('to-cart', null, $brut_prices);
    }

    protected function apply($method, $Order, &$brut_prices)
    {
        $start   = strtotime($this->getValue('start_time') . ' 00:00:00');
        $endDate = $this->getValue('end_date');
        $end     = $endDate != '' && $endDate != '0000-00-00' ? strtotime($endDate . ' 23:59:59') : null;
        $value   = $this->getValue('discount_value');
        $percent = $this->getValue('discount_percent');
        $orders  = array_filter((array)$this->getValue('orders'));
        $user    = Customer::getCurrentUser();

        // calculate residual balance
        if ($this->getValue('is_multi_use') == 3) {
            if ($user) {
                foreach ($orders as $orderId => $orderDiscount) {
                    $order        = Order::get($orderId);
                    $customerData = $order->getCustomerData();

                    if ($customerData->getId() == $user->getId()) {
                        throw new CouponException('Coupon already used', 5);
                    }
                }
            }
        } else if ($this->getValue('is_multi_use') == 2 && count($orders)) {
            throw new CouponException('Coupon consumed', 2);
        } else if ($this->getValue('is_multi_use') != 1 && $value && count($orders)) {
            $_value = $value;
            foreach ($orders as $order_id => $order_discount) {
                $value -= (float)$order_discount;
            }
            $this->setValue('discount_value', $value);
        }

        // do some checks
        if ($this->getValue('is_multi_use') == 0 && count($orders) && ($value <= 0 || $percent)) {
            throw new CouponException('Coupon consumed', 2);
        } else if ($start > time()) {
            throw new CouponException('Coupon not yet valid', 3);
        } else if ($end && $end <= time()) {
            throw new CouponException('Coupon not valid anymore', 4);
        }

        if ($method == 'to-order') {
            $discount = parent::applyToOrder($Order, $brut_prices, 'coupon');
        } else if ($method == 'to-cart') {
            $discount = parent::applyToCart($brut_prices);
        }

        if (isset ($_value)) {
            $this->setValue('discount_value', $_value);
        }
        return $discount;
    }

    public function linkToOrder($Order)
    {
        $orders = (array)$this->getValue('orders');
        $value  = $this->getValue('value');
        $total  = $Order->getValue('initial_total');

        foreach ($orders as $order_id => $order_discount) {
            $value -= (float)$order_discount;
        }
        $orders[$Order->getValue('id')] = $total < $value ? $total : $value;

        $this->setValue('status', 'givenaway');
        $this->setValue('orders', json_encode($orders));
        $this->save();
    }

    public static function getByCode($code)
    {
        if (trim($code) == '') {
            return false;
        }
        return self::query()
            ->whereRaw('(code = :w1 AND prefix = "") OR CONCAT(prefix, "-", code) = :w1', ['w1' => $code])
            ->findOne();
    }

    public function ext_applyDiscounts(\rex_extension_point $Ep)
    {
        $promotions           = $Ep->getSubject();
        $promotions['coupon'] = $this;
        return $promotions;
    }

    public function getCode()
    {
        $code = $this->getValue("prefix");
        return $code != "" ? $code . "-" . $this->getValue("code") : $this->getValue("code");
    }

    public static function ext_completeOrder(\rex_extension_point $ep)
    {
        if ($ep->getSubject()) {
            $newCoupons = [];
            $order      = $ep->getParam('Order');
            $extras     = $order->getValue('extras', false, []);
            $promotions = $order->getValue('promotions');

            foreach ($promotions as $promotion) {
                if ($promotion->getValue('action') == 'coupon_code' && $promotion->valueIsset('coupon_code')) {
                    $coupon       = current(self::cloneCode($promotion->getValue('coupon_code'), 1, 'autogenerated'));
                    $newCoupons[] = $coupon;
                }
            }

            if ($newCoupons) {
                $extras['generated_coupons'] = $newCoupons;
            } else {
                unset($extras['generated_coupons']);
            }
            $order->setValue('extras', $extras, false);
            $order->save();
        }
    }

    public static function ext_beforeSendOrder(\rex_extension_point $ep)
    {
        $mail   = $ep->getParam('Mail');
        $order  = $ep->getParam('Order');
        $extras = $order->getValue('extras');

        if (class_exists('Kreatif\Mpdf\Mpdf') && isset($extras['generated_coupons'])) {
            $mail->setVar('coupons', $extras['generated_coupons']);
        }
    }
}

class CouponException extends \Exception
{
    public function getLabelByCode()
    {
        switch ($this->getCode()) {
            case 1:
                $errors = '###error.coupon_not_exists###';
                break;
            case 2:
                $errors = '###error.coupon_consumed###';
                break;
            case 3:
                $errors = '###error.coupon_not_yet_valid###';
                break;
            case 4:
                $errors = '###error.coupon_not_valid_anymore###';
                break;
            case 5:
                $errors = '###error.coupon_already_used###';
                break;
            default:
                $errors = $this->getMessage();
                break;
        }
        return $errors;
    }
}