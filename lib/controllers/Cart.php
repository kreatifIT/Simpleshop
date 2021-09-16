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
            'check_cart'      => true,
            'apply_discounts' => true,
            'apply_coupon'    => true,
            'products'        => null,
        ], $this->params);

        if (\rex_request::isXmlHttpRequest()) {
            $this->params = array_merge($this->params, rex_request('api-params', 'array', []));
        }


        $errors      = [];
        $discount    = 0;
        $postAction  = rex_request('action', 'string');
        $minOrderVal = self::getMinOrderValue();

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
                $coupon_code = trim(rex_request('coupon_code', 'string'));
                Session::setCheckoutData('coupon_code', $coupon_code);
                break;
        }

        if (count($this->products)) {
            $promotions  = [];
            $order       = Session::getCurrentOrder();
            $customer    = Customer::getCurrentUser();
            $shipping    = $order->getValue('shipping');
            $grossTotals = Session::getGrossTotals();
            $coupon_code = Session::getCheckoutData('coupon_code');
            $Coupon      = $coupon_code != '' ? Coupon::getByCode($coupon_code) : null;

            if (!$shipping || !$shipping->getValue('hasCosts')) {
                foreach (Shipping::getAll() as $_shipping) {
                    if ($_shipping->getValue('hasCosts')) {
                        $shipping = $_shipping;
                        break;
                    }
                }
            }
            if (!$order->getValue('customer_data')) {
                if (!$customer) {
                    $customer = Customer::create();
                }
                // prepare customer_data for discount validation
                $order->setValue('customer_data', $customer);
            }

            if ($minOrderVal > array_sum($grossTotals)) {
                $errors [] = ['label' => str_replace('{VALUE}', '<strong>' . format_price($minOrderVal) . ' &euro;</strong>', \Wildcard::get('label.min_order_info'))];
            }

            if ($shipping) {
                $address = $order->getShippingAddress();
                $order->setValue('products', $this->products);

                if (!$address) {
                    $address = $customer ? $customer->getShippingAddress() : CustomerAddress::create();
                }
                if (isset($this->params['country-id'])) {
                    Session::setCheckoutData('cart_country_id', $this->params['country-id']);
                }
                else {
                    $this->params['country-id'] = Session::getCheckoutData('cart_country_id', $address ? $address->getValue('country') : null);
                }
                $shipping->setValue('country_id', $this->params['country-id']);
                $order->setValue('shipping', $shipping);

                if (!$order->getValue('shipping_address')) {
                    $order->setValue('shipping_address', $address);
                }
            }

            if ($this->params['apply_coupon']) {
                if ($Coupon) {
                    try {
                        $Coupon->applyToOrder($order, $order->getValue('brut_prices'));
                    }
                    catch (CouponException $ex) {
                        Session::setCheckoutData('coupon_code', null);
                        $errors[] = ['label' => $ex->getLabelByCode()];
                    }
                }
                else if ($coupon_code != '') {
                    Session::setCheckoutData('coupon_code', null);
                    $errors[] = ['label' => '###error.coupon_not_exists###'];
                }
            }

            if ($this->params['apply_discounts']) {
                try {
                    $_errors    = $order->recalculateDocument($this->products);
                    $errors     = array_merge($errors, $_errors);
                    $promotions = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.applyDiscounts', [], [
                        'Order'      => $order,
                        'products'   => $this->products,
                        'country_id' => $this->params['country-id'],
                    ]));
                    $_errors    = $order->recalculateDocument($this->products, $promotions);
                    $errors     = array_merge($errors, $_errors);
                    $discount   = $order->getValue('discount');
                }
                catch (OrderException $ex) {
                    $errors[] = ['label' => $ex->getMessage()];
                }
                catch (\Exception $ex) {
                    $errors[] = ['label' => $ex->getLabelByCode()];
                }

                $upsellingPromotions = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.getUpsellingPromotion', [], [
                    'Order'      => $order,
                    'country_id' => $this->params['country-id'],
                ]));
            }

            foreach ($this->params as $key => $value) {
                $this->setVar($key, $value);
            }

            if (count($errors)) {
                $this->errors = array_merge($this->errors, $errors);
            }

            $this->setVar('products', $this->products);
            $this->setVar('totals', $grossTotals);
            $this->setVar('discount', $discount);
            $this->setVar('shipping', $shipping);
            $this->setVar('shipping_costs', $order->getShippingCosts());
            $this->setVar('promotions', $promotions);
            $this->setVar('upselling_promotions', $upsellingPromotions, false);
            $this->fragment_path[] = 'simpleshop/cart/table-wrapper.php';
        } else {
            $this->fragment_path[] = 'simpleshop/cart/empty.php';
        }
    }

    public static function getMinOrderValue()
    {
        $value = Settings::getValue('min_order_value', 'general');

        return (float) strtr($value, [',' => '.']);
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