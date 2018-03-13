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

?>
<div class="addresses-grid margin-top margin-large-bottom">
    <div class="row column">
        <h2>###label.shipping_address###</h2>

        <?php
        $this->setVar('form_field_type', 'invoice');
        $this->setVar('btn_label', '###action.go_ahead###');
        $this->setVar('callback_on_save', function ($output, $form) {
            $nextStep = CheckoutController::getNextStep();
            $Order    = Session::getCurrentOrder();
            $Address  = CustomerAddress::get($form->getObjectParams('main_id'));

            // load data
            $Address->getValue('created');

            $Order->setShippingAddress($Address);
            Session::setCheckoutData('Order', $Order);

            CheckoutController::setDoneStep($nextStep);
            rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep]));
        });
        $this->subfragment('simpleshop/customer/customer_area/address_form.php');
        ?>
    </div>
</div>