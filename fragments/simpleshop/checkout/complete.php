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

$Order   = $this->getVar('order');
$Payment = $Order->getValue('payment');
$Shiping = $Order->getValue('shipping');

?>

    <div class="row column">
        <h2 class="text-center margin-small-bottom">###shop.order_placed###</h2>
    </div>


<?php

if ($Order->getValue('total') > 0)
{
    $this->subfragment('simpleshop/payment/' . $Payment->plugin_name . '/order_complete.php');
}
$this->subfragment('simpleshop/shipping/' . $Shiping->plugin_name . '/order_complete.php');

// CLEAR THE SESSION
Session::clearCheckout();
Session::clearCart();