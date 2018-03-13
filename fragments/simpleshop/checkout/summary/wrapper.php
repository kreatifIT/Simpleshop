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


$Settings   = \rex::getConfig('simpleshop.Settings');
$Config     = FragmentConfig::getValue('checkout');
$Order      = $this->getVar('Order');
$errors     = $this->getVar('errors', []);
$back_url   = $this->getVar('back_url');
$warnings   = $this->getVar('warnings', []);
$promotions = $Order->getValue('promotions');
$payment    = $Order->getValue('payment');
$shipping   = $Order->getValue('shipping');


?>
<div class="summary-wrapper">

    <div class="row column margin-top margin-bottom">
        <h2>###simpleshop.summary_order###</h2>
    </div>

    <?php if (count($warnings)): ?>
        <div class="row column">
            <?php foreach ($warnings as $warning): ?>
                <div class="callout alert margin-bottom">
                    <p><?= isset($warning['replace']) ? strtr($warning['label'], ['{{replace}}' => $warning['replace']]) : $warning['label'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (count($errors)): ?>
        <div class="row column margin-large-bottom">
            <div class="callout alert margin-bottom">
                <p><?= implode('<br/>', $errors) ?></p>
            </div>
            <div class="margin-large-bottom text-center">
                <a href="<?= rex_getUrl($Settings['linklist']['cart']) ?>" class="button">###action.go_back###</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (count($errors) == 0): ?>

        <?php
        $this->subfragment('simpleshop/checkout/summary/address_wrapper.php');
        ?>

        <?php
        if ($shipping || $payment) {
            echo '<div class="row margin-bottom">';
        }
        if ($shipping) {
            $this->setVar('shipping', $shipping);
            $this->subfragment('simpleshop/checkout/summary/shipping.php');
        }

        if ($payment) {
            $this->setVar('payment', $payment);
            $this->subfragment('simpleshop/checkout/summary/payment.php');
        }
        if ($shipping || $payment) {
            echo '</div>';
        } ?>

        <?php
        if ($Config['has_coupons']) // coupons
        {
            $this->subfragment('simpleshop/checkout/summary/coupon.php');
        }
        ?>

        <?php if ($promotions): ?>
            <div class="discounts row column margin-bottom">

                <h3>###simpleshop.promotions###</h3>
                <p>###simpleshop.applied_promotion_text###</p>
                <?php
                foreach ($promotions as $promotion) {
                    $this->setVar('promotion', $promotion);
                    $this->subfragment('simpleshop/checkout/summary/discount_item.php');
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Warenkorb -->
        <div class="summary-items row column">
            <?php
            $Controller = CartController::execute([
                'cart_config'               => array_merge(FragmentConfig::getValue('cart'), [
                    'has_remove_button' => false,
                ]),
                'cart_button_config'        => array_merge(FragmentConfig::getValue('cart.button'), [
                    'has_quantity_control' => false,
                    'has_quantity'         => true,
                ]),
                'cart_table_wrapper_config' => array_merge(FragmentConfig::getValue('cart.table-wrapper'), [
                    'has_go_ahead' => false,
                ]),
            ]);
            echo $Controller->parse();
            ?>
        </div>

        <!-- Summe -->
        <?php
        $this->subfragment('simpleshop/checkout/summary/conclusion.php');
        ?>

        <div class="summary-footer">
            <form action="" method="post">
                <!-- AGB -->
                <div class="terms-of-service row column text-right">
                    <div class="custom-checkbox align-right margin-small-bottom">
                        <label>
                            *&nbsp;###label.tos###
                            <input name="tos_accepted" value="1" type="checkbox"/>
                            <span class="checkbox"></span>
                        </label>
                    </div>
                    <div class="custom-checkbox align-right margin-bottom">
                        <label>
                            *&nbsp;###simpleshop.cancellation_terms###
                            <input name="rma_accepted" value="1" type="checkbox"/>
                            <span class="checkbox"></span>
                        </label>
                    </div>
                </div>

                <div class="row column margin-top margin-large-bottom">
                    <a href="<?= $back_url ?>" class="button">###action.go_back###</a>
                    <button type="submit" name="action" value="place_order" class="margin-bottom secondary button float-right">
                        ###action.place_order###
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

</div>