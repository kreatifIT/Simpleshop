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

$action   = rex_get('payment_action', 'string');
$Order    = $this->getVar('order');
$Payment  = $Order->getValue('payment');
$order_id = $Order->getValue('id');

if ($action == 'complete')
{

}
else
{
    $Payment->initPayment($order_id, $Order->getValue('total'), 'Order #' . $order_id);
}
