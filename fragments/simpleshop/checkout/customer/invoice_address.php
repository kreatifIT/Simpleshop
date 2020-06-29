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

?>
<div class="addresses-grid margin-top margin-large-bottom">
    <h2 class="heading large">###label.invoice_address###</h2>

    <?php
    $this->setVar('save_callback', function ($address) {
        \FriendsOfREDAXO\Simpleshop\CheckoutController::processInvoiceAddress($address);
    });
    $this->subfragment('simpleshop/customer/customer_area/invoice_address.php');
    ?>

</div>
