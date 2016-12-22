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
    protected static $session      = NULL;
    protected static $has_shipping = FALSE;
    public static    $errors       = [];

    public static function cleanupSessions()
    {
        $sessions      = [];
        $sql           = \rex_sql::factory();
        $_this         = parent::create();
        $session_files = glob(ini_get('session.save_path') . '/*');
        // call getSession to merge possible duplicate sessions
        self::getSession();
        foreach ($session_files as $_session)
        {
            list ($path, $session) = explode('/sess_', $_session);
            $sessions[] = $session;
        }
        $sql->setQuery("DELETE FROM " . $_this->getTableName() . " WHERE session_id NOT IN('" . implode("', '", $sessions) . "')");
    }

    public static function getCheckoutData($key = NULL, $default = NULL)
    {
        if ($key)
        {
            return array_key_exists($key, $_SESSION['checkout']) && $_SESSION['checkout'][$key] !== NULL ? $_SESSION['checkout'][$key] : $default;
        }
        else
        {

            return $_SESSION['checkout'];
        }
    }

    public static function setCheckoutData($key, $value)
    {
        return $_SESSION['checkout'][$key] = $value;
    }

    public static function getCurrentOrder()
    {
        if (!isset ($_SESSION['checkout']['Order']))
        {
            $_SESSION['checkout']['Order'] = Order::create();
        }
        else if (!$_SESSION['checkout']['Order']->exists() && $_SESSION['checkout']['Order']->getValue('id'))
        {
            // to prevent duplicate key errors on resaving orders
            $_SESSION['checkout']['Order'] = Order::get($_SESSION['checkout']['Order']->getValue('id'));
        }
        return $_SESSION['checkout']['Order'];
    }

    public static function getSession()
    {
        if (self::$session === NULL)
        {
            $session_id    = session_id();
            $User          = Customer::getCurrentUser();
            $session       = parent::query()->where('session_id', $session_id)->findOne();
            self::$session = $session ?: parent::create();

            if ($User)
            {
                $user_session = parent::query()
                    ->where('customer_id', $User->getValue('id'))
                    ->where('session_id', $session_id, '!=')
                    ->findOne();

                if ($user_session)
                {
                    // merge sessions because we found to sessions for the same user
                    $cart_items = self::_getCartItems(TRUE, self::$session) + self::_getCartItems(TRUE, $user_session);
                    self::$session->writeSession(['cart_items' => $cart_items]);
                    // remove the previous session
                    $user_session->delete();
                }
            }
        }
        return self::$session;
    }

    public function writeSession($data = [])
    {
        $User = Customer::getCurrentUser();

        foreach ($data as $key => $value)
        {
            if ($key == 'cart_items')
            {
                $value = json_encode($value);
                $this->setValue('last_cart_update', date('Y-m-d H:i:s'));
            }
            $this->setValue($key, $value);
        }
        $this
            ->setValue('lastupdate', date('Y-m-d H:i:s'))
            ->setValue('session_id', session_id());

        if ($User)
        {
            $this->setValue('customer_id', $User->getValue('id'));
        }
        $this->save();
    }

    private static function _getCartItems($raw = FALSE, $throwErrors = TRUE)
    {
        $session    = self::getSession();
        $cart_items = (array) $session->getValue('cart_items');


        if (!$raw)
        {
            $results            = [];
            self::$has_shipping = FALSE;

            foreach ($cart_items as $key => $item)
            {
                try
                {
                    $product            = Product::getProductByKey($key, $item['quantity'], $item['extras']);
                    self::$has_shipping = self::$has_shipping || $product->getValue('type') == 'product';
                }
                catch (ProductException $ex)
                {
                    if ($throwErrors)
                    {
                        throw new ProductException($ex->getMessage(), $ex->getCode());
                    }
                }
                if ($product)
                {
                    $results[] = $product;
                }
            }
            $cart_items = $results;
        }
        return $cart_items;
    }

    public static function getCartItems($raw = FALSE, $throwErrors = TRUE)
    {
        $label_name   = sprogfield('name');
        self::$errors = [];

        do
        {
            try
            {
                $products = self::_getCartItems($raw, $throwErrors);
                $retry    = FALSE;
            }
            catch (ProductException $ex)
            {
                $retry = TRUE;
                $msg   = $ex->getMessage();
                $key   = substr($msg, strrpos($msg, '--key:') + 6);
                $code  = $ex->getCode();

                switch ($code)
                {
                    case 1:
                        // product does not exist any more
                    case 2:
                        // feature does not exist any more
                    case 3:
                        // variant-combination does not exist
                        Session::removeProduct($key);
                        self::$errors['cart_product_not_exists']['label'] = checkstr(Wildcard::get('simpleshop.error.cart_product_not_exists'), '###simpleshop.error.cart_product_not_exists###');
                        self::$errors['cart_product_not_exists']['replace'] += 1;
                        break;

                    case 4:
                        // product availability is null
                        Session::removeProduct($key);
                        list ($product_id, $feature_ids) = explode('|', trim($key, '|'));
                        $product        = Product::get($product_id);
                        $label          = strtr(checkstr(Wildcard::get('simpleshop.error.cart_product_not_available'), '###simpleshop.error.cart_product_not_available###'), ['{{replace}}' => $product->getValue($label_name)]);
                        self::$errors[] = ['label' => $label];
                        break;

                    case 5:
                        // not enough products
                        $product = Product::getProductByKey($key, 0);
                        // update cart
                        Session::setProductData($key, $product->getValue('amount'));
                        $label          = strtr(checkstr(Wildcard::get('simpleshop.error.cart_product_not_enough_amount'), '###simpleshop.error.cart_product_not_enough_amount###'), [
                            '{{replace}}' => $product->getValue($label_name),
                            '{{count}}'   => $product->getValue('amount'),
                        ]);
                        self::$errors[] = ['label' => $label];
                        break;

                    default:
                        throw new ProductException($msg, $code);
                        break;
                }
            }
        }
        while ($retry);

        if (count(self::$errors))
        {
            throw new CartException('Product errors', 1);
        }
        if (!$raw)
        {
            self::setCheckoutData('has_shipping', self::$has_shipping);
        }
        return array_filter($products);
    }

    public static function getProductKey($product_id, $feature_value_ids = [])
    {
        $feature_value_ids = !is_array($feature_value_ids) ? [$feature_value_ids] : $feature_value_ids;
        return $product_id . '|' . implode(',', $feature_value_ids);
    }

    public static function addProduct($product_key, $quantity = 1, $extras = [])
    {
        $cart_items = self::_getCartItems(TRUE);
        self::setProductData($product_key, $cart_items[$product_key]['quantity'] + $quantity, $extras);
    }

    public static function setProductData($product_key, $quantity, $extras = [])
    {
        $session    = self::getSession();
        $cart_items = self::_getCartItems(TRUE);
        // update the quantity
        $cart_items[$product_key]['quantity'] = $quantity;
        // update extras
        $cart_items[$product_key]['extras'] = array_merge((array) $cart_items[$product_key]['extras'], $extras);
        $session->writeSession(['cart_items' => $cart_items]);
    }

    public static function removeProduct($product_key)
    {
        $session    = self::getSession();
        $cart_items = self::_getCartItems(TRUE);
        unset($cart_items[$product_key]);
        $session->writeSession(['cart_items' => $cart_items]);
    }

    public static function clearCart()
    {
        $session = self::getSession();
        $session->writeSession(['cart_items' => []]);
    }

    public static function clearCheckout()
    {
        $_SESSION['checkout'] = [];
    }
}


class CartException extends \Exception
{
}