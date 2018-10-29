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

$_FUNC    = rex_get('func', 'string');
$order_id = rex_get('data_id', 'int');


if ($_FUNC == 'print') {
    \rex_response::cleanOutputBuffers();

    $Order = Order::get($order_id);
    $PDF   = $Order->getPackingListPDF(false);

    $PDF->Output();
    exit;
}


