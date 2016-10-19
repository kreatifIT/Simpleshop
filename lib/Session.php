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

class Session extends \rex_yform_manager_dataset
{
    const TABLE = 'rex_shop_session';
    protected static $session = NULL;
    public static $errors  = [];

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
        $cart_items = (array) json_decode($session->getValue('cart_items'), TRUE);

        if (!$raw)
        {
            $results = [];
            foreach ($cart_items as $key => $item)
            {
                try
                {
                    $product = Product::getProductByKey($key, $item['quantity']);
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

                switch ($ex->getCode())
                {
                    case 1:
                        // product does not exist any more
                    case 2:
                        // feature does not exist any more
                    case 3:
                        // variant-combination does not exist
                        Session::removeProduct($key);
                        self::$errors['cart_product_not_exists']['label'] = Wildcard::get('simpleshop.error.cart_product_not_exists');
                        self::$errors['cart_product_not_exists']['replace'] += 1;
                        break;

                    case 4:
                        // product availability is null
                        Session::removeProduct($key);
                        list ($product_id, $feature_ids) = explode('|', trim($key, '|'));
                        $product        = Product::get($product_id);
                        $label          = strtr(Wildcard::get('simpleshop.error.cart_product_not_available'), ['{{replace}}' => $product->getValue($label_name)]);
                        self::$errors[] = ['label' => $label];
                        break;

                    case 5:
                        // not enough products
                        $product = Product::getProductByKey($key, 0);
                        // update cart
                        Session::setProductQuantity($key, $product->getValue('amount'));
                        $label = strtr(Wildcard::get('simpleshop.error.cart_product_not_enough_amount'), [
                            '{{replace}}' => $product->getValue($label_name),
                            '{{count}}'   => $product->getValue('amount'),
                        ]);
                        self::$errors[] = ['label' => $label];
                        break;

                    default:
                        throw new ProductException($msg, $ex->getCode());
                        break;
                }
            }
        }
        while ($retry);

        if (count(self::$errors))
        {
            throw new CartException('Product errors', 1);
        }
        return $products;
    }

    public static function getProductKey($product_id, $feature_value_ids = [])
    {
        $feature_value_ids = !is_array($feature_value_ids) ? [$feature_value_ids] : $feature_value_ids;
        return $product_id . '|' . implode(',', $feature_value_ids);
    }

    public static function addProduct($product_key, $quantity = 1)
    {
        $cart_items = self::_getCartItems(TRUE);
        self::setProductQuantity($product_key, $cart_items[$product_key]['quantity'] + $quantity);
    }

    public static function setProductQuantity($product_key, $quantity)
    {
        $session    = self::getSession();
        $cart_items = self::_getCartItems(TRUE);
        // update the quantity
        $cart_items[$product_key]['quantity'] = $quantity;
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
}


class CartException extends \Exception
{
}