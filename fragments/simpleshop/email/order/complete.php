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

$Order = $this->getVar('Order');
$Payment = $Order->getValue('payment');
$Shipping = $Order->getValue('shipping');
$this->setVar('order', $Order);

?>
    <p>###shop.email.order_complete_text###</p>
<?php

$this->subfragment('simpleshop/payment/' . $Payment->plugin_name . '/order_complete.php');
$this->subfragment('simpleshop/shipping/' . $Shipping->plugin_name . '/order_complete.php');
$this->subfragment('simpleshop/customer/customer_area/order/detail.php');