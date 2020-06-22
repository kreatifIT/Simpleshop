<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 16.06.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop\Ombis;

class Order
{

    public static function createPreVKDokument(\FriendsOfREDAXO\Simpleshop\Order $order)
    {
        pr($order);

    }

    public static function ext__createPreVKDokument(\rex_extension_point $ep)
    {
        $saveSuccess = $ep->getSubject();

        if ($saveSuccess) {
            $order = $ep->getParam('Order');
            $saveSuccess = self::createPreVKDokument($order);
        }
        $ep->setSubject($saveSuccess);
    }
}