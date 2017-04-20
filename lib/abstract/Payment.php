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

class Payment extends Plugin
{
    protected static $type = 'payment';
}


abstract class PaymentAbstract extends PluginAbstract
{
    public    $plugin_name;
    protected $name  = '';
    protected $price = 0;

    public function getName()
    {
        return $this->name;
    }

    public function getPrice($products = null)
    {
        return $this->price;
    }

    public function canBePayedNow()
    {
        return method_exists($this, 'initPayment');
    }

    public static function get()
    {
        return Payment::getByClass(get_called_class());
    }
}