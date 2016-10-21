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
    public $plugin_name;

    public abstract function getPrice();
    public abstract function getName();

    public static function get()
    {
        return Shipping::getByClass(get_called_class());
    }

    public function getData()
    {

    }
}