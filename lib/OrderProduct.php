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

class OrderProduct extends Model
{
    const TABLE = 'rex_shop_order_products';
    protected $object_data = [
        'data',
    ];

    public static function create($table = NULL)
    {
        $_this = parent::create($table);

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.OrderProduct.getObjectData', $_this->object_data));

        return $_this;
    }
}