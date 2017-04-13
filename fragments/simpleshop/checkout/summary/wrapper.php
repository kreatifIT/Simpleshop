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
$cart_url = $this->getVar('cart_url');
$errors   = $this->getVar('errors', []);
$warnings = $this->getVar('warnings', []);

$invoice_addr  = $Order->getInvoiceAddress();
$shipping_addr = $Order->getShippingAddress();
$shipping      = $Order->getValue('shipping');
$payment       = $Order->getValue('payment');
$promotions    = $Order->getValue('promotions');
$extras        = $Order->getValue('extras');


if (count($warnings)): ?>
    <div class="row column">
        <?php foreach ($warnings as $warning): ?>
            <div class="callout alert margin-bottom">
                <p><?= isset($warning['replace']) ? strtr($warning['label'], ['{{replace}}' => $warning['replace']]) : $warning['label'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if (count($errors)): ?>
    <div class="row column">
        <div class="callout alert margin-bottom">
            <p><?= implode('<br/>', $errors) ?></p>
        </div>
        <div class="margin-bottom text-center">
            <a href="<?= $cart_url ?>">###action.go_back###</a>
        </div>
    </div>
<?php endif; ?>

<?php if (count($errors) == 0): ?>
    <!-- Adressen -->
    <div class="row address-panels">
    <h3>###label.summary_order###</h3>

    <?php
    if ($invoice_addr) {
        $this->setVar('title', '###shop.invoice_address###');
        $this->setVar('url', rex_getUrl(null, null, ['step' => 2]));
        $this->setVar('address', $invoice_addr);
        if (Session::getCheckoutData('as_guest')) {
            $this->setVar('email', $extras['address_extras']['email']);
        }
        $this->subfragment('simpleshop/checkout/summary/address_item.php');
    }
    ?>

    <?php
    if ($shipping && $shipping_addr) {
        $this->setVar('title', '###shop.shipping_address###');
        $this->setVar('url', rex_getUrl(null, null, ['step' => 2]));
        if ($extras['address_extras']['use_shipping_address']) {
            $this->setVar('address', $shipping_addr);
        }
        else {
            $this->setVar('address', $invoice_addr);
        }
        $this->subfragment('simpleshop/checkout/summary/address_item.php');
    }
    ?>

    <?php if ($shipping): ?>
        </div>

        <!-- Lieferung & Zahlung -->
        <div class="row radio-panels">
    <?php endif; ?>

    <?php if ($shipping): ?>
        <div class="medium-6 columns margin-bottom">
            <h3>###label.shipment###</h3>

            <?php
            $before = '
            <a href="' . rex_getUrl(null, null, ['step' => 3]) . '" class="edit">
                <i class="fa fa-pencil hide-for-large" aria-hidden="true"></i>
                <span class="show-for-large">###action.edit###</span>
            </a>
        ';
            $this->setVar('before', $before, false);
            $this->setVar('shipping', $shipping);
            $this->setVar('name', $shipping->getName());
            $this->setVar('plugin_name', $shipping->getPluginName());
            $this->subfragment('simpleshop/checkout/shipping_and_payment/shipping_item.php');
            ?>
        </div>
    <?php endif; ?>

    <?php if ($payment): ?>
        <div class="medium-6 columns margin-bottom <?php if (!$shipping) {
            echo 'radio-panels no-shipping';
        } ?>">
            <h3>###label.payment###</h3>

            <?php
            $before = '
            <a href="' . rex_getUrl(null, null, ['step' => 3]) . '" class="edit">
                <i class="fa fa-pencil hide-for-large" aria-hidden="true"></i>
                <span class="show-for-large">###action.edit###</span>
            </a>
        ';
            $this->setVar('before', $before, false);
            $this->setVar('payment', $payment);
            $this->setVar('name', $payment->getName());
            $this->setVar('plugin_name', $payment->getPluginName());
            $this->subfragment('simpleshop/checkout/shipping_and_payment/payment_item.php');
            ?>
        </div>
    <?php endif; ?>
    </div>

    <?php
    // coupons
    $this->subfragment('simpleshop/checkout/summary/coupon.php');
    ?>

    <?php if ($promotions): ?>
        <div class="discounts row column margin-bottom">

            <h3>###shop.promotions###</h3>
            <p>###shop.applied_promotion_text###</p>
            <?php
            foreach ($promotions as $promotion) {
                $this->setVar('promotion', $promotion);
                $this->subfragment('simpleshop/checkout/summary/discount_item.php');
            }
            ?>
        </div>
    <?php endif; ?>

    <!-- Warenkorb -->
    <div class="shop row column">

        <?= CartController::execute([
            'config' => [
                'has_quantity_control' => false,
                'has_quantity'         => true,
                'has_remove_button'    => false,
            ],
        ]); ?>

    </div>

    <!-- Summe -->
    <?php
    $this->subfragment('simpleshop/checkout/summary/conclusion.php');
    ?>


    <form action="" method="post">
        <!-- AGB -->
        <div class="terms-of-service row column">
            <div class="custom-checkbox margin-small-bottom">
                <label>
                    * ###label.tos###
                    <input name="tos_accepted" value="1" type="checkbox"/>
                    <span class="checkbox"></span>
                </label>
            </div>
            <div class="custom-checkbox">
                <label>
                    * ###label.cancellation_terms###
                    <input name="rma_accepted" value="1" type="checkbox"/>
                    <span class="checkbox"></span>
                </label>
            </div>
        </div>

        <div class="row buttons-checkout margin-bottom">
            <div class="medium-6 medium-offset-6 columns">
                <button type="submit" name="action" value="place_order" class="button button-checkout">
                    ###action.place_order###
                </button>
            </div>
        </div>
    </form>
<?php endif; ?>
