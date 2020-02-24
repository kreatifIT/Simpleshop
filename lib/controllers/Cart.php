<?php

/**
 * This file is part of the FriendsOfREDAXO\Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 * Date: 23.03.17
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;


class CartController extends Controller
{
    protected $products = [];

    public function _execute()
    {
        $this->params = array_merge([
            'check_cart' => true,
            'products'   => null,
        ], $this->params);

        $errors     = [];
        $discount   = 0;
        $postAction = rex_request('action', 'string');

        try {
            if ($this->params['products'] === null) {
                $this->products = Session::getCartItems(false, $this->params['check_cart']);
            } else {
                $this->products = $this->params['products'];
            }
        } catch (CartException $ex) {
            if ($ex->getCode() == 1) {
                $errors = Session::$errors;
            }
            $this->products = Session::getCartItems();
        }

        switch ($postAction) {
            case 'redeem_coupon':
                $coupon_code = trim(rex_get('coupon_code', 'string'));

                Session::setCheckoutData('coupon_code', $coupon_code);
                break;
        }

        $grossTotals  = Session::getGrossTotals();
        $coupon_code  = Session::getCheckoutData('coupon_code');
        $grossTotals2 = $grossTotals;
        $Coupon       = $coupon_code != '' ? Coupon::getByCode($coupon_code) : null;

        if ($Coupon) {
            try {
                $discount = $Coupon->applyToCart($grossTotals2);
            } catch (CouponException $ex) {
                Session::setCheckoutData('coupon_code', null);
                $errors[] = ['label' => $ex->getLabelByCode()];
            }
        } else if ($coupon_code != '') {
            Session::setCheckoutData('coupon_code', null);
            $errors[] = ['label' => '###simpleshop.error.coupon_not_exists###'];
        }

        if (count($errors)) {
            $this->errors = array_merge($this->errors, $errors);
        }

        foreach ($this->params as $key => $value) {
            $this->setVar($key, $value);
        }

        if (count($this->products)) {
            $this->setVar('products', $this->products);
            $this->setVar('totals', $grossTotals);
            $this->setVar('discount', $discount);
            $this->fragment_path[] = 'simpleshop/cart/table-wrapper.php';
        } else {
            $this->fragment_path[] = 'simpleshop/cart/empty.php';
        }
    }

    public function getProducts()
    {
        return $this->products;
    }

    public static function ext_project_layoutBottom(\rex_extension_point $ep)
    {
        $subject  = $ep->getSubject();
        $fragment = new \rex_fragment();

        $subject .= $fragment->parse('simpleshop/cart/offcanvas/wrapper.php');
        return $subject;
    }
}