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

class Order extends \rex_yform_manager_dataset
{
    const TABLE = 'rex_shop_order';
    private $object_data = [
        'address_1',
        'address_2',
        'shipping',
        'payment',
    ];

    public static function create($table = NULL)
    {
        $_this             = parent::create($table);
        $_this->createdate = date('Y-m-d H:i:s');

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.getObjectData', $_this->object_data));

        return $_this;
    }

    public function getValue($key, $process = TRUE)
    {
        $value = parent::getValue($key);

        if ($process && in_array($key, $this->object_data) && is_string($value))
        {
            $_data  = json_decode($value);
            $Object = call_user_func([$_data->class, 'create']);
            $data   = (array) $_data->data;

            foreach ($data as $name => $value)
            {
                $Object->setValue($name, $value);
            }
            $value = $Object;
        }
        return $value;
    }

    public function save()
    {
        foreach ($this->object_data as $name)
        {
            $object = $this->getValue($name, FALSE);
            if (is_object($object))
            {
                $class_name = get_class($object);
                $this->setValue($name, json_encode(['class' => $class_name, 'data' => $object->getData()]));
            }
        }
        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.preSave', $this));

        return parent::save();
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