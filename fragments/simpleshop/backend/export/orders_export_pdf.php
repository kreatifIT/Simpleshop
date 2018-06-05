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

use Kreatif\Mpdf\Mpdf;


$buffer    = '';
$PDF       = new Mpdf([], true);
$order_ids = $this->getVar('order_ids');
$statuses  = $this->getVar('statuses');
$orders    = Order::query()->where('id', $order_ids)->where('invoice_num', 0, '>')->orderBy('createdate')->find();
$orderCnt  = count($orders);

foreach ($orders as $index => $order) {
    $PDF = $order->getInvoicePDF('invoice', false, $PDF);

    if ($orderCnt > $index + 1) {
        $PDF->WriteHTML('<pagebreak />');
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OUTPUT
echo $PDF->Output('', 'S');