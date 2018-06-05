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

$Order    = $this->getVar('Order');
$CNOrder  = Order::get($Order->getValue('ref_order_id'));
$Shipping = $Order->getValue('shipping');


?>
<p>###shop.email.creditnote_text###</p>
