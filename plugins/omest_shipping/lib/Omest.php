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
    const NAME = '###shop.omest_shipping###';

    public function getPrice($products)
    {
        foreach ($products as $product)
        {
            $data = [
                'weight' => $product->getValue('weight'),
                'length' => $product->getValue('length'),
                'height' => $product->getValue('height'),
                'width'  => $product->getValue('width'),
            ];
            pr($data);
        }
        return 5;
    }

    public function getName()
    {
        return self::NAME;
    }
}