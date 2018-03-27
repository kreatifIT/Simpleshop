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
$config   = $this->getVar('cart_table_wrapper_config', FragmentConfig::getValue('cart.table-wrapper'));
$settings = \rex::getConfig('simpleshop.Settings');

?>
<table class="cart <?= $config['class'] ?>" data-cart-item-container="">
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

<?php if ($config['has_go_ahead'] || strlen($config['back_url'])): ?>
    <div class="cart-buttons clearfix">
        <?php if (strlen($config['back_url'])): ?>
            <a href="<?= $config['back_url'] ?>" class="cart-button-back button">
                ###action.continue_shopping###
            </a>
        <?php endif; ?>

        <?php if ($config['has_go_ahead']): ?>
            <a href="<?= rex_getUrl($settings['linklist']['checkout']) ?>" class="cart-button-proceed button <?= $config['btn_ahead_class'] ?>">
                ###action.proceed_to_checkout###
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>
