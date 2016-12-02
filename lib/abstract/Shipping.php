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

class Shipping extends Plugin
{
    protected static $type = 'shipping';
}


abstract class ShippingAbstract extends PluginAbstract
{
    public    $plugin_name;
    protected $name           = '';
    protected $price          = 0;
    protected $tax            = 0;
    protected $tax_percentage = 0;

    public function getName()
    {
        return $this->name;
    }

    public function getPrice($products = NULL)
    {
        return $this->price;
    }

    public function getTax()
    {
        return $this->tax;
    }

    public function getTaxPercentage()
    {
        return $this->tax_percentage;
    }

    public static function get()
    {
        return Shipping::getByClass(get_called_class());
    }
}