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

class Omest extends ShippingAbstract
{
    protected $price = 5;
    protected $name  = '###shop.standard_shipping###';

    public function getPrice()
    {
        return $this->price;
    }

    public function getName()
    {
        return $this->name;
    }
}