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

$Addon = \rex_addon::get('simpleshop');
$_FUNC = rex_request('func', 'string');

if ($_FUNC == 'update')
{
    $products = rex_post('quantity', 'array', []);
    foreach ($products as $key => $quantity)
    {
        Session::setProductQuantity($key, $quantity);
    }
}
else if ($_FUNC == 'remove')
{
    Session::removeProduct(rex_get('key'));
    header('Location: ' . rex_getUrl());
    exit();
}

?>
<form action="" method="post">
    <table class="cart-content stack">
        <thead>
        <tr class="cart-header">
            <th><?= $Addon->i18n('label.preview'); ?></th>
            <th><?= $Addon->i18n('label.product'); ?></th>
            <th><?= $Addon->i18n('label.price'); ?></th>
            <th><?= $Addon->i18n('label.amount'); ?></th>
            <th><?= $Addon->i18n('label.total'); ?></th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $products = \FriendsOfREDAXO\Simpleshop\Session::getCartItems();

        foreach ($products as $product)
        {
            $this->setVar('product', $product);
            echo $this->subfragment('product/general/cart/item.php');
        }
        ?>

        </tbody>
    </table>
</form>