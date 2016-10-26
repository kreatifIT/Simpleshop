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

    public static function redeem($code, $Order)
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
        $promotions           = $Order->getValue('promotions');
        $promotions['coupon'] = $_this;
        $Order->setValue('promotions', $promotions);
        return $_this;
    }

    public function applyToOrder($Order)
    {
        $start = strtotime($this->getValue('start_time'));
        $end   = $this->getValue('end_time') != '' ? strtotime($this->getValue('end_time')) : NULL;

        // do some checks
        if ($this->getValue('count') <= 0)
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
        return parent::applyToOrder($Order);
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