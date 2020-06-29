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
$Config     = $this->getVar('Config');
$Order      = $this->getVar('Order');
$products   = $this->getVar('products', []);
$errors     = $this->getVar('errors', []);
$back_url   = $this->getVar('back_url');
$warnings   = $this->getVar('warnings', []);
$promotions = $Order->getValue('promotions');
$payment    = $Order->getValue('payment');
$shipping   = $Order->getValue('shipping');

?>
<div class="summary-wrapper">

    <div class="margin-top margin-bottom">
        <h2>###simpleshop.your_order_num###</h2>
    </div>

    <?php if (count($warnings)): ?>
        <div class="callout alert margin-bottom">
            <?php foreach ($warnings as $warning): ?>
                - <?= isset($warning['replace']) ? strtr($warning['label'], ['{{replace}}' => $warning['replace']]) : $warning['label'] ?><br/>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (count($errors)): ?>
        <div class="margin-large-bottom">
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
            echo '<div class="grid-x grid-margin-x margin-bottom">';
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
            <div class="discounts margin-bottom">

                <h3>###label.promotions###</h3>
                <p>###label.applied_promotion_text###</p>
                <?php
                foreach ($promotions as $promotion) {
                    $this->setVar('promotion', $promotion);
                    $this->subfragment('simpleshop/checkout/summary/discount_item.php');
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Warenkorb -->
        <div class="checkout-summary-items margin-bottom">
            <?php
            $Controller = CartController::execute([
                'products'                  => $products,
                'use_tax_prices'            => false,
                'cart_config'               => array_merge(FragmentConfig::getValue('cart'), [
                    'has_remove_button' => false,
                ]),
                'cart_button_config'        => array_merge(FragmentConfig::getValue('cart.button'), [
                    'has_quantity_control' => false,
                    'has_quantity'         => true,
                ]),
                'cart_table_wrapper_config' => array_merge(FragmentConfig::getValue('cart.table-wrapper'), [
                    'show_coupon_info'  => false,
                    'has_remove_button' => false,
                    'hide_summary'      => true,
                    'has_go_ahead'      => false,
                ]),
            ]);
            echo $Controller->parse();
            ?>
        </div>

        <!-- Summe -->
        <?php
        $this->subfragment('simpleshop/checkout/summary/conclusion.php');
        ?>

        <?php if ($Config['has_summary_footer']): ?>
            <div class="checkout-summary-footer">
                <form action="<?= rex_getUrl(null, null, array_merge($_GET, ['ts' => time()])) ?>" method="post">
                    <!-- AGB -->
                    <div class="terms-of-service text-right">
                        <div class="custom-checkbox align-right margin-small-bottom">
                            <label>
                                *&nbsp;###action.accept_tos###
                                <input name="tos_accepted" value="1" type="checkbox"/>
                                <span class="checkbox"></span>
                            </label>
                        </div>
                        <div class="custom-checkbox align-right margin-small-bottom">
                            <label>
                                *&nbsp;###label.cancellation_terms###
                                <input name="rma_accepted" value="1" type="checkbox"/>
                                <span class="checkbox"></span>
                            </label>
                        </div>
                        <span class="required-fields-hint">* ###label.are_required_fields###</span>
                    </div>

                    <div class="margin-top">
                        <label>###label.remarks_infos###</label>
                        <textarea rows="3" name="remarks"><?= $Order->getValue('remarks') ?></textarea>
                    </div>

                    <div class="margin-top margin-large-bottom">
                        <a href="<?= $back_url ?>" class="button hollow">###action.go_back###</a>
                        <button type="submit" name="action" value="place_order" class="button margin-bottom float-right">
                            ###action.place_order###
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

