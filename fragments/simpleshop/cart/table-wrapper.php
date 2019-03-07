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

$products = $this->getVar('products', []);
$discount = $this->getVar('discount', 0);
$totals   = $this->getVar('totals', []);
$config   = $this->getVar('cart_table_wrapper_config', FragmentConfig::getValue('cart.table-wrapper'));

$tax         = 0;
$coupon_code = Session::getCheckoutData('coupon_code');
$styles      = FragmentConfig::getValue('styles');
$settings    = \rex::getConfig('simpleshop.Settings');
$Coupon      = Coupon::getByCode($coupon_code);

?>
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
    <div class="cart-coupon">
        <div class="coupon-input-container">
            <input type="text" class="coupon-input" placeholder="###label.insert_coupon###" data-link="<?= rex_getUrl() ?>" value="<?= $coupon_code ?>">
            <button class="button coupon-submit" type="submit" onclick="Simpleshop.applyCoupon(this, '.cart-coupon|.coupon-input', '.cart-container');">
                <span>###label.use_coupon###</span>
                <i class="fal fa-chevron-circle-right"></i>
            </button>
        </div>

        <?php if ($Coupon): ?>
            <div class="label success">
                "<strong><?= $Coupon->getName() ?></strong>"
                ###label.applied###
            </div>
        <?php endif; ?>
    </div>

    <div class="cart-sum">
        <table>
            <tbody>

            <tr>
                <td>
                    <span>###simpleshop.brutto_total###</span>
                    <?php if ($discount > 0): ?>
                        <span>###label.discount###</span>
                    <?php endif; ?>
                    <span>###label.tax###</span>
                </td>
                <td>
                    <span><?= format_price(array_sum($totals) + $discount) ?> &euro;</span>
                    <?php if ($discount > 0): ?>
                        <span>-<?= format_price($discount) ?> &euro;</span>
                    <?php endif; ?>
                    <span>
                            <?php
                            foreach ($totals as $_tax => $total) {
                                $tax += $total * ($_tax / 100);
                            }
                            ?>
                        +<?= format_price($tax) ?> &euro;
                        </span>
                </td>
            </tr>
            <tr>
                <td>
                    <span>###simpleshop.total_sum###</span>
                </td>
                <td>
                    <span><?= format_price(array_sum($totals) + $tax) ?> &euro;</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if ($config['has_go_ahead'] || strlen($config['back_url'])): ?>
    <div class="cart-buttons clearfix">
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
