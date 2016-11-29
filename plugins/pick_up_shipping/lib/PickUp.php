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

class PickUp extends ShippingAbstract
{
    const NAME  = '###shop.shipping_pickup_point###';
    const PRICE = 0;

    public function getPrice($products)
    {
        return self::PRICE;
    }

    public function getName()
    {
        return self::NAME;
    }
}