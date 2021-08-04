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


use Sprog\Wildcard;


class Session extends Model
{
    const TABLE = 'rex_shop_session';

    protected static $session      = null;
    protected static $has_shipping = false;
    public static    $errors       = [];

    public static function cleanupSessions()
    {
        $sql = \rex_sql::factory();
        $sql->setQuery("DELETE FROM " . self::TABLE . " WHERE lastupdate < '" . date('Y-m-d H:i:s', strtotime('-30 Days')) . "'");
    }

    public static function getCheckoutData($key = null, $default = null)
    {
        $checkout = rex_session('checkout', 'array');

        if ($key) {
            return array_key_exists($key, $checkout) && $checkout[$key] !== null ? $checkout[$key] : $default;
        } else {
            return $checkout;
        }
    }

    public static function setCheckoutData($key, $value)
    {
        $checkout       = self::getCheckoutData();
        $checkout[$key] = $value;
        rex_set_session('checkout', $checkout);
    }

    public static function getCurrentOrder()
    {
        $order = self::getCheckoutData('Order');

        if (!$order) {
            $order = Order::create();
        } else if (!$order->exists() && $order->getValue('id')) {
            // to prevent duplicate key errors on resaving orders
            $order = Order::get($order->getValue('id'));
        }
        self::setCheckoutData('Order', $order);
        return $order;
    }

    public static function getSession()
    {
        if (self::$session === null) {
            $session_id    = session_id();
            $session       = parent::query()
                ->where('session_id', $session_id)
                ->findOne();
            self::$session = $session ?: parent::create();
        }
        return self::$session;
    }

    public function writeSession($data = [])
    {
        if (\rex::isBackend()) {
            return;
        }
        $User = Customer::getCurrentUser();

        foreach ($data as $key => $value) {
            if ($key == 'cart_items') {
                $value = json_encode($value);
                $this->setValue('last_cart_update', date('Y-m-d H:i:s'));
            }
            $this->setValue($key, $value);
        }
        $this->setValue('lastupdate', date('Y-m-d H:i:s'))
            ->setValue('session_id', session_id());

        if ($User) {
            $this->setValue('customer_id', $User->getValue('id'));
        }
        $this->save();

        $stmt = \rex_sql::factory();
        $stmt->setTable(Session::TABLE);

        if ($User) {
            $stmt->setWhere('id != :id AND (session_id = :sid OR customer_id = :cid)', [
                'id'  => $this->getId(),
                'sid' => session_id(),
                'cid' => $User->getId(),
            ]);
        } else {
            $stmt->setWhere('id != :id AND session_id = :sid', [
                'id'  => $this->getId(),
                'sid' => session_id(),
            ]);
        }
        $stmt->delete();
    }

    private static function _getCartItems($raw = false, $throwErrors = false)
    {
        $session    = self::getSession();
        $cart_items = (array)$session->getValue('cart_items');


        if (!$raw) {
            $results            = [];
            self::$has_shipping = false;

            foreach ($cart_items as $key => $item) {
                if (is_array($item)) {
                    try {
                        $product = Product::getProductByKey($key, $item['quantity'], $item['extras']);
                        $product = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Session.getCartItem', $product, []));

                    } catch (ProductException $ex) {
                        if ($throwErrors) {
                            throw new ProductException($ex->getMessage(), $ex->getCode());
                        }
                    }
                    if ($product) {
                        self::$has_shipping = self::$has_shipping || $product->getValue('type') == 'product';
                        $results[]          = $product;
                    }
                }
            }
            $cart_items = $results;
        }
        return $cart_items;
    }

    public static function getCartItems($raw = false, $throwErrors = true)
    {
        self::$errors = [];

        do {
            try {
                $products = self::_getCartItems($raw, $throwErrors);
                $retry    = false;
            } catch (ProductException $ex) {
                $retry = true;
                $msg   = $ex->getMessage();
                $key   = substr($msg, strrpos($msg, '--key:') + 6);
                $code  = $ex->getCode();

                switch ($code) {
                    case 1:
                        // product does not exist any more
                    case 2:
                        // feature does not exist any more
                    case 3:
                        // variant-combination does not exist
                        Session::removeProduct($key);
                        self::$errors['cart_product_not_exists']['label']   = Wildcard::get('error.cart_product_not_exists');
                        self::$errors['cart_product_not_exists']['replace'] += 1;
                        break;

                    case 4:
                        // product availability is null
                        Session::removeProduct($key);
                        list ($product_id, $feature_ids) = explode('|', trim($key, '|'));
                        $product        = Product::get($product_id);
                        $label          = strtr(Wildcard::get('error.cart_product_not_available'), ['{{replace}}' => $product->getName()]);
                        self::$errors[] = ['label' => $label];
                        break;

                    case 5:
                        // not enough products
                        $product = Product::getProductByKey($key, 0);
                        // update cart
                        Session::setProductData($key, $product->getValue('amount'));
                        $label          = strtr(Wildcard::get('error.cart_product_not_enough_amount'), [
                            '{{replace}}' => $product->getName(),
                            '{{count}}'   => $product->getValue('amount'),
                        ]);
                        self::$errors[] = ['label' => $label];
                        break;

                    default:
                        throw new ProductException($msg, $code);
                        break;
                }
            }
        } while ($retry);

        if (count(self::$errors)) {
            throw new CartException('Product errors', 1);
        }
        if (!$raw) {
            self::setCheckoutData('has_shipping', self::$has_shipping);
        }
        return array_filter($products);
    }

    public static function getCartItemCount($raw = true, $throwErrors = false)
    {
        $cartItems   = self::getCartItems($raw, $throwErrors);
        $cartItemCnt = 0;
        if ($cartItems) {
            foreach ($cartItems as $item) {
                $cartItemCnt += isset($item['quantity']) ? $item['quantity'] : 0;
            }
        }
        return $cartItemCnt;
    }

    public static function getProductKey($product_id, $feature_value_ids = [])
    {
        $feature_value_ids = !is_array($feature_value_ids) ? [$feature_value_ids] : $feature_value_ids;
        return $product_id . '|' . implode(',', $feature_value_ids);
    }

    public static function getTotal($include_tax = true)
    {
        $total    = 0;
        $products = self::_getCartItems();

        foreach ($products as $product) {
            $cart_quantity = $product->getValue('cart_quantity');
            $price         = $product->getPrice($include_tax);
            $total         += ($cart_quantity * $price);
        }
        return $total;
    }

    public static function getNetTotals()
    {
        $totals   = [];
        $products = self::_getCartItems();

        foreach ($products as $product) {
            $tax = Tax::get($product->getValue('tax'))
                ->getValue('tax');

            $cart_quantity = $product->getValue('cart_quantity');
            $price         = $product->getPrice(false);
            $totals[$tax]  += ($cart_quantity * $price);
        }
        return $totals;
    }

    public static function getGrossTotals()
    {
        $totals   = [];
        $products = self::_getCartItems();

        foreach ($products as $product) {
            $tax = Tax::get($product->getValue('tax'))
                ->getValue('tax');

            $cart_quantity = $product->getValue('cart_quantity');
            $price         = $product->getPrice(true);
            $totals[$tax]  += ($cart_quantity * $price);
        }
        return $totals;
    }

    public static function addProduct($product_key, $quantity = 1, $extras = [])
    {
        $cart_items = self::_getCartItems(true, false);
        self::setProductData($product_key, $cart_items[$product_key]['quantity'] + $quantity, $extras);
    }

    public static function setProductData($product_key, $quantity, $extras = [])
    {
        $session    = self::getSession();
        $cart_items = self::_getCartItems(true, false);
        // update the quantity
        $cart_items[$product_key]['quantity'] = $quantity;
        // update extras
        $cart_items[$product_key]['extras'] = array_merge((array)$cart_items[$product_key]['extras'], $extras);
        $session->writeSession(['cart_items' => $cart_items]);
    }

    public static function removeProduct($product_key)
    {
        $session    = self::getSession();
        $cart_items = self::_getCartItems(true, false);
        unset($cart_items[$product_key]);
        $session->writeSession(['cart_items' => $cart_items]);
    }

    public static function clearCart()
    {
        $session = self::getSession();
        $session->writeSession(['cart_items' => []]);
        self::setCheckoutData('coupon_code', null);
    }

    public static function clearCheckout()
    {
        rex_set_session('checkout', []);
    }

    public static function getGAProducts()
    {
        $products = [];
        $items    = Session::getCartItems();

        foreach ($items as $product) {
            $variantName = [];
            $variantId   = explode("|", $product->getValue('key'));
            $variantId   = $variantId[1];
            $variants    = $product->getFeatureVariants();
            $Variant     = $variants['variants'][$variantId] ?: $product;
            $features    = $Variant->getValue('features', false, []);

            foreach ($features as $data) {
                if (!$data->nameIsEmpty()) {
                    $variantName[] = $data->getName();
                }
            }

            $products[] = [
                'id'       => $product->getId(),
                'name'     => $product->getName(),
                'category' => $product->getUrl([], \rex_clang::getCurrentId()),
                'quantity' => $product->getValue('cart_quantity'),
                'variant'  => implode(', ', $variantName),
                'price'    => number_format($Variant->getPrice(true), 2, '.', ','),
            ];
        }
        return $products;
    }
}


class CartException extends \Exception
{
}