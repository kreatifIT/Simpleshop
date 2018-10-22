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

$Order         = $this->getVar('Order');
$add_info      = $this->getVar('additional_info');
$Payment       = $Order->getValue('payment');
$Shipping      = $Order->getValue('shipping');

FragmentConfig::$data['cart']['has_remove_button'] = false;
FragmentConfig::$data['cart']['button']['has_quantity_control'] = false;
FragmentConfig::$data['cart']['table-wrapper']['has_go_ahead'] = false;

$styles = FragmentConfig::getValue('email_styles');

?>
<div class="order-complete-email">
    <h2>###simpleshop.email.order_complete_text###</h2>

    <?php
    if ($Payment) {
        $this->subfragment('simpleshop/checkout/payment/' . $Payment->plugin_name . '/order_complete.php');
    }
    if ($Shipping) {
        $this->subfragment('simpleshop/checkout/shipping/' . $Shipping->plugin_name . '/order_complete.php');

        if ($Shipping->hasCosts()) {
            $this->setVar('shipping_costs', $Order->getValue('shipping_costs'));
        }
    }
    ?>

    <?php if (strlen($add_info)): ?>
        <p><?= $add_info ?></p>
    <?php endif; ?>

    <?php if ($Payment): ?>
        <!-- payment -->
        <table <?= $styles['table'] ?>>
            <tr <?= $styles['tr'] ?>>
                <th>
                    <h3 <?= $styles['h2'] ?>>###label.payment_method###</h3>
                    <p <?= $styles['p'] ?>><?= $Payment->getName() ?><br/><?= $Payment->getValue('info') ?></p>
                </th>
                <th class="expander" style="<?= $styles['th'] ?>"></th>
            </tr>
        </table>
    <?php endif; ?>


    <?php if ($Shipping): ?>
        <!-- shipping -->
        <table <?= $styles['table'] ?>>
            <tr <?= $styles['tr'] ?>>
                <th>
                    <h3 <?= $styles['h2'] ?>>###label.shipping_method###</h3>
                    <p <?= $styles['p'] ?>><?= $Shipping->getName() ?></p>
                </th>
                <th class="expander" style="<?= $styles['th'] ?>"></th>
            </tr>
        </table>
    <?php endif; ?>


</div>
