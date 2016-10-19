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

use Symfony\Component\Yaml\Exception\RuntimeException;

class Shipping
{
    protected static $shipping_classes = [];
    protected static $shipping_plugins = [];

    public static function registerShipping($class, $plugin_name)
    {
        self::$shipping_classes[$class]       = ['plugin_name' => $plugin_name, 'this' => NULL];
        self::$shipping_plugins[$plugin_name] = $class;
    }

    public static function get($plugin_name)
    {
        return self::getByClass(self::$shipping_plugins[$plugin_name]);
    }

    public static function getByClass($class)
    {
        if (!array_key_exists($class, self::$shipping_classes))
        {
            throw new RuntimeException("'{$class}' is not as shipping registered");
        }
        if (self::$shipping_classes[$class]['this'] === NULL)
        {
            $_this = new $class();
            // check if is extended from abstract
            if ($_this instanceof ShippingAbstract)
            {
                $_this->plugin_name = self::$shipping_classes[$class]['plugin_name'];
                self::$shipping_classes[$class]['this'] = $_this;
            }
            else
            {
                throw new RuntimeException("'{$class}' must be instance of ShippingAbstact");
            }
        }
        return self::$shipping_classes[$class]['this'];
    }

    public static function getAll()
    {
        $shippings = [];
        foreach (self::$shipping_classes as $class => $data)
        {
            $shippings[] = self::getByClass($class);
        }
        return $shippings;
    }
}


abstract class ShippingAbstract
{
    public $plugin_name;

    public abstract function getPrice();

    public abstract function getName();

    public static function get()
    {
        return Shipping::getByClass(get_called_class());
    }

    public function getPluginName()
    {
        return $this->plugin_name;
    }
}