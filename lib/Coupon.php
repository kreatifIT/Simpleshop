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

class Coupon extends Discount
{
    const TABLE = 'rex_shop_coupon';

    public static function redeem($code)
    {
        if ($code == '')
        {
            return FALSE;
        }
        $_this = self::getByCode($code);

        if (!$_this)
        {
            throw new CouponException('Coupon not exists', 1);
        }
        \rex_extension::register('simpleshop.Order.calculateDocument', [$_this, 'ext_calculateDocument']);

        return $_this;
    }

    public static function createGiftcard($Order, $Product)
    {
        $order_id   = $Order->getValue('id');
        $label_name = sprogfield('name');

        $_this = self::create();
        $_this->setValue('code', ''); // IMPORTANT! reset the code to generate a nice one
        $_this->setValue($label_name, 'Giftcard Order #' . $order_id);
        $_this->setValue('given_away', 1);
        $_this->setValue('discount_value', $Product->getPrice());
        $_this->setValue('prefix', 'OR');
        $_this->setValue('orders', $order_id);
        $_this->setValue('start_time', $Order->getValue('status') != 'OP' ? date('Y-m-d') : '9999-12-31');
        $_this->save();

        // set code to product
        $extras                = $Product->getValue('extras');
        $extras['coupon_code'] = $_this->prefix . '-' . $_this->code;
        $Product->setValue('extras', $extras);

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Coupon.createGiftcard.CREATED', $_this, ['Order' => $Order, 'Product' => $Product]));
    }

    public function applyToOrder($Order)
    {
        $start   = strtotime($this->getValue('start_time'));
        $end     = $this->getValue('end_time') != '' ? strtotime($this->getValue('end_time')) : NULL;
        $value   = $this->getValue('discount_value');
        $percent = $this->getValue('discount_percent');
        $orders  = (array) $this->getValue('orders');

        // calculate residual balance
        if ($value && count($orders))
        {
            $_value = $value;
            foreach ($orders as $order_id => $order_discount)
            {
                $value -= $order_discount;
            }
            $this->setValue('discount_value', $value);
        }

        // do some checks
        if (count($orders) && ($value <= 0 || $percent))
        {
            throw new CouponException('Coupon consumed', 2);
        }
        else if ($start > time())
        {
            throw new CouponException('Coupon not yet valid', 3);
        }
        else if ($end && $end <= time())
        {
            throw new CouponException('Coupon not valid anymore', 4);
        }
        $result = parent::applyToOrder($Order);

        if (isset ($_value))
        {
            $this->setValue('discount_value', $_value);
        }
        return $result;
    }

    public function linkToOrder($Order)
    {
        $orders = (array) $this->getValue('orders');
        $value  = $this->getValue('discount_value');
        $total  = $Order->getValue('initial_total');

        foreach ($orders as $order_id => $order_discount)
        {
            $value -= $order_discount;
        }
        $orders[$Order->getValue('id')] = $total < $value ? $total : $value;

        $this->setValue('given_away', 1);
        $this->setValue('orders', json_encode($orders));
        $this->save();
    }

    public static function getByCode($code)
    {
        if (trim($code) == '')
        {
            return FALSE;
        }
        return self::query()
            ->whereRaw('(code = :w1 AND prefix = "") OR CONCAT(prefix, "-", code) = :w1', ['w1' => $code])
            ->findOne();
    }

    public function ext_calculateDocument($params)
    {
        $Order                = $params->getSubject();
        $promotions           = $Order->getValue('promotions');
        $promotions['coupon'] = $this;
        $Order->setValue('promotions', $promotions);
        return $Order;
    }
}

class CouponException extends \Exception
{
    public function getLabelByCode()
    {
        switch ($this->getCode())
        {
            case 1:
                $errors = '###shop.error.coupon_not_exists###';
                break;
            case 2:
                $errors = '###shop.error.coupon_consumed###';
                break;
            case 3:
                $errors = '###shop.error.coupon_not_yet_valid###';
                break;
            case 4:
                $errors = '###shop.error.coupon_not_valid_anymore###';
                break;
            default:
                $errors = $this->getMessage();
                break;
        }
        return $errors;
    }
}