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


use Kreatif\Model;


class OrderProduct extends Model
{
    const TABLE = 'rex_shop_order_products';

    protected $object_data = [
        'data',
    ];

    public static function getByProductId($orderId, $productId)
    {
        $query = OrderProduct::query();
        $query->where('order_id', $orderId);
        $query->where('product_id', $productId);
        return $query->findOne();
    }

    public function save($prepare = false)
    {
        $result = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.OrderProduct.preSave', true, ['this' => $this]));
        if ($result) {
            $result = parent::save($prepare);
        }
        return $result;
    }
}