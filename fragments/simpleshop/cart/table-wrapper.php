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
$config   = array_merge([
    'class'              => '',
    'email_tpl_styles'   => [],
    'has_image'          => true,
    'has_remove_button'  => true,
    'has_refresh_button' => true,
], $this->getVar('config', []));

$this->setVar('config', $config);

?>
<form action="" method="post">
    <table class="cart-content stack">
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
</form>
