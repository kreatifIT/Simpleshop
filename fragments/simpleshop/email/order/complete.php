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
$remarks  = trim($Order->getValue('remarks'));
$Payment  = $Order->getValue('payment');
$Shipping = $Order->getValue('shipping');
$coupons  = $this->getVar('coupons', []);

FragmentConfig::$data['cart']['has_remove_button']              = false;
FragmentConfig::$data['cart']['button']['has_quantity_control'] = false;
FragmentConfig::$data['cart']['table-wrapper']['has_go_ahead']  = false;

$styles = FragmentConfig::getValue('email_styles');

?>
<div class="order-complete-email">
    <h2>###label.email__order_complete_text###</h2>

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



    <?php if (strlen($remarks)): ?>
        <br/>
        <div class="remarks">
            <h3 <?= $styles['h2'] ?>>###label.remarks_infos###</h3>
            <div style="font-size:13px;line-height:17px;"><?= nl2br($remarks) ?></div>
        </div>
    <?php endif; ?>


    <?php if (count($coupons)): ?>
        <br>
        <p>
            ###label.email__generated_coupons_message###
        </p>
        <?php foreach ($coupons as $coupon): ?>
            <div class="coupon" <?= $styles['coupon_wrapper'] ?>>
                <div class="heading" <?= $styles['coupon_heading'] ?>><?= $coupon->getName() ?></div>
                <div class="code" <?= $styles['coupon_code'] ?>><?= $coupon->getCode() ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
