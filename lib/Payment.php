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

class Payment
{
    protected static $payment_classes = [];
    protected static $payment_plugins = [];

    public static function registerPayment($class, $plugin_name)
    {
        self::$payment_classes[$class]       = ['plugin_name' => $plugin_name, 'this' => NULL];
        self::$payment_plugins[$plugin_name] = $class;
    }

    public static function get($plugin_name)
    {
        return self::getByClass(self::$payment_plugins[$plugin_name]);
    }

    public static function getByClass($class)
    {
        if (!array_key_exists($class, self::$payment_classes))
        {
            throw new RuntimeException("'{$class}' is not as payment registered");
        }
        if (self::$payment_classes[$class]['this'] === NULL)
        {
            $_this = new $class();
            // check if is extended from abstract
            if ($_this instanceof PaymentAbstract)
            {
                $_this->plugin_name = self::$payment_classes[$class]['plugin_name'];
                self::$payment_classes[$class]['this'] = $_this;
            }
            else
            {
                throw new RuntimeException("'{$class}' must be instance of PaymentAbstract");
            }
        }
        return self::$payment_classes[$class]['this'];
    }

    public static function getAll()
    {
        $payments = [];
        foreach (self::$payment_classes as $class => $data)
        {
            $payments[] = self::getByClass($class);
        }
        return $payments;
    }
}


abstract class PaymentAbstract
{
    public $plugin_name;

    public abstract function getPrice();

    public abstract function getName();

    public static function get()
    {
        return Payment::getByClass(get_called_class());
    }

    public function getPluginName()
    {
        return $this->plugin_name;
    }
}