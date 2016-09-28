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

class Session extends \rex_yform_manager_dataset
{
    protected static $session = NULL;

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
            $user_session  = parent::query()
                ->where('customer_id', $User->getValue('id'))
                ->where('session_id', $session_id, '!=')
                ->findOne();
            self::$session = $session ?: parent::create();

            if ($user_session)
            {
                // merge sessions because we found to sessions for the same user
                $cart_items = self::getCartItems(TRUE, self::$session) + self::getCartItems(TRUE, $user_session);
                self::$session->writeSession(['cart_items' => $cart_items]);
                // remove the previous session
                $user_session->delete();
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

    public static function getCartItems($raw = FALSE, $session = NULL)
    {
        $session    = $session ?: self::getSession();
        $cart_items = (array) json_decode($session->getValue('cart_items'), TRUE);

        if (!$raw)
        {
            $results = [];
            foreach ($cart_items as $key => $item)
            {
                list ($product_id, $feature_ids) = explode('|', $key);
                $_result     = ['key' => $key, 'amount' => $item, 'product' => Product::get($product_id), 'features' => []];
                $feature_ids = $feature_ids ? explode(',', $feature_ids) : [];

                foreach ($feature_ids as $feature_id)
                {
                    $_result['features'][] = Feature::get($feature_id);
                }
                $results[] = $_result;
            }
            $cart_items = $results;
        }
        return $cart_items;
    }

    public static function getProductKey($product_id, $feature_value_ids = [])
    {
        return $product_id . '|' . implode(',', $feature_value_ids);
    }

    public static function addProduct($product_key, $quantity = 1)
    {
        $cart_items = self::getCartItems(TRUE);
        self::setProductQuantity($product_key, $cart_items[$product_key]['amount'] + $quantity);
    }

    public static function setProductQuantity($product_key, $quantity)
    {
        $session    = self::getSession();
        $cart_items = self::getCartItems(TRUE);
        // update the quantity
        $cart_items[$product_key]['amount'] = $quantity;
        $session->writeSession(['cart_items' => $cart_items]);
    }

    public static function removeProduct($product_key)
    {
        $session    = self::getSession();
        $cart_items = self::getCartItems(TRUE);
        unset($cart_items[$product_key]);
        $session->writeSession(['cart_items' => $cart_items]);
    }

    public static function clearCart()
    {
        $session = self::getSession();
        $session->writeSession(['cart_items' => []]);
    }
}