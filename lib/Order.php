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

class Order extends Model
{
    const TABLE = 'rex_shop_order';

    public static function create($table = NULL)
    {
        $_this = parent::create($table);

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.getObjectData', $_this->object_data));

        return $_this;
    }


    public function save()
    {
        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.preSave', $this));

        $result = parent::save();

        if ($result)
        {
            $order_id = $this->getValue('id');
            $products = Session::getCartItems(FALSE, FALSE);

            // clear all products first
            \rex_sql::factory()->setQuery("DELETE FROM " . OrderProduct::TABLE . " WHERE order_id = {$order_id}");

            // set order products
            foreach ($products as $product)
            {
                $prod_data = Model::prepare($product);

                foreach ($prod_data as $name => $value)
                {
                    $product->setValue($name, $value);
                }
                OrderProduct::create()
                    ->setValue('order_id', $order_id)
                    ->setValue('product_id', $product->getValue('id'))
                    ->setValue('data', $product)
                    ->save(TRUE);
            }
        }
        return $result;
    }

    public function calculateDocument()
    {
        $errors         = [];
        $this->tax      = 0;
        $this->subtotal = 0;

        try
        {
            $products = Session::getCartItems();
        }
        catch (CartException $ex)
        {
            if ($ex->getCode() == 1)
            {
                $errors   = Session::$errors;
                $products = Session::getCartItems();
            }
        }
        foreach ($products as $product)
        {
            $quantity = $product->getValue('cart_quantity');
            $this->subtotal += (float) $product->getPrice(TRUE) * $quantity;
            $this->tax += (float) $product->getTax() * $quantity;
        }
        $this->shipping_costs = (float) $this->shipping ? $this->shipping->getPrice() : 0;
        $this->status         = 'OP';
        $this->updatedate     = date('Y-m-d H:i:s');
        $this->ip_address     = rex_server('REMOTE_ADDR', 'string', 'notset');
        $this->total          = $this->subtotal + $this->shipping_costs - $this->discount;

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.calculateDocument', $this));

        return $errors;
    }
}