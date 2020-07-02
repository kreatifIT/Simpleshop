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

use Kreatif\Model\Country;


$products      = $this->getVar('products', []);
$discount      = $this->getVar('discount', 0);
$promotions    = $this->getVar('promotions', []);
$upsPromotions = $this->getVar('upselling_promotions', []);
$shipping      = $this->getVar('shipping', null);
$shippingPrice = $this->getVar('shipping_costs', 0);
$totals        = $this->getVar('totals', []);
$config        = $this->getVar('cart_table_wrapper_config', FragmentConfig::getValue('cart.table-wrapper'));

$coupon_code = Session::getCheckoutData('coupon_code');
$styles      = FragmentConfig::getValue('styles');
$settings    = \rex::getConfig('simpleshop.Settings');
$hasCoupons  = FragmentConfig::getValue('cart.has_coupons');
$cartArticle = Settings::getArticle('cart');


?>
<div data-cart-container>
    <?php if (count($promotions)): ?>
        <div class="callout success">
            <strong>###label.applied_promotion_text###</strong><br/>
            <?php foreach ($promotions as $promotion): ?>
                - <?= $promotion->getName() ?><br/>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (count($upsPromotions)): ?>
        <div class="callout secondary">
            <?php foreach ($upsPromotions as $upsPromotion): ?>
                <?= $upsPromotion['message'] ?><br/>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <table class="cart <?= $config['class'] ?>" data-cart-item-container <?= $styles['table'] ?>>
        <thead>
        <?= $this->subfragment('simpleshop/cart/table-head.php'); ?>
        </thead>
        <tbody>
        <?php
        foreach ($products as $product) {
            $this->setVar('product', $product);
            echo $this->subfragment('simpleshop/cart/item.php');
        }
        ?>
        </tbody>
    </table>

    <?php if (!$config['hide_summary']): ?>
        <div class="cart-sum grid-x grid-margin-x align-bottom">
            <div class="cell large-6">
                <?php if ($hasCoupons): ?>
                    <div class="cart-coupon" data-coupon-wrapper>
                        <div class="coupon-input-container">
                            <input type="text" class="coupon-input" placeholder="###label.insert_coupon###" data-coupon-input data-link="<?= $cartArticle->getUrl() ?>" value="<?= $coupon_code ?>">
                            <button class="button coupon-submit" type="submit" onclick="Simpleshop.applyCoupon(this, '[data-coupon-wrapper]|[data-coupon-input]', '[data-cart-container]');">
                                <span>###action.use_coupon###</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cell large-6">
                <div class="cart-summary-total">

                    <?php if ($shipping || $discount > 0): ?>
                        <div class="subtotal">
                            <span class="label">###label.subtotal###</span>
                            <span class="price">&euro;&nbsp;<?= format_price(array_sum($totals)) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($shipping): ?>
                        <?php
                        $query = Country::query();
                        $query->where('status', 1);
                        $query->orderBy('prio', 'asc');
                        $countries = $query->find();
                        ?>
                        <?php if (count($countries) > 1): ?>
                            <div class="total">
                                <span class="label">###label.shipping_country###</span>
                                <span class="price">
                                <select onchange="Simpleshop.refreshCart(this)" name="country-id">
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= $country->getId() ?>" <?= $country->getId() == $shipping->getValue('country_id') ? 'selected="selected"' : '' ?>>
                                    <?= $country->getName() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                            </span>
                            </div>
                        <?php endif; ?>
                        <div class="total">
                            <span class="label">###label.shipping_costs###</span>
                            <span class="price">&euro;&nbsp;<?= format_price($shippingPrice) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($discount > 0): ?>
                        <div class="promotions ">
                            <span class="label">###label.discount###</span>
                            <span class="price">&euro;&nbsp;-<?= format_price($discount) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="total">
                        <span class="label">###label.total_sum###</span>
                        <span class="price">&euro;&nbsp;<?= format_price(array_sum($totals) + $shippingPrice - $discount) ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($config['has_go_ahead'] || strlen($config['back_url'])): ?>
        <div class="cart-buttons">
            <?php if (strlen($config['back_url'])): ?>
                <a href="<?= $config['back_url'] ?>" class="cart-button-back button">
                    ###action.continue_shopping###
                </a>
            <?php endif; ?>

            <?php if ($config['has_go_ahead']): ?>
                <a href="<?= rex_getUrl($settings['linklist']['checkout'], null, ['ts' => time()]) ?>" class="cart-button-proceed button">
                    ###action.proceed_to_checkout###
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>