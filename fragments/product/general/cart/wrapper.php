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

$errors  = [];
$Addon   = \rex_addon::get('simpleshop');
$lang_id = \rex_clang::getCurrentId();
$_FUNC   = rex_request('func', 'string');

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

do
{
    try
    {
        $products = \FriendsOfREDAXO\Simpleshop\Session::getCartItems();
        $retry    = FALSE;
    }
    catch (ProductException $ex)
    {
        $retry = TRUE;
        $msg   = $ex->getMessage();
        $key   = substr($msg, strrpos($msg, '--key:') + 6);

        switch ($ex->getCode())
        {
            case 1:
                // product does not exist any more
            case 2:
                // feature does not exist any more
            case 3:
                // variant-combination does not exist
                Session::removeProduct($key);
                $errors['error.cart_product_not_exists']['label'] = $Addon->i18n('error.cart_product_not_exists');
                $errors['error.cart_product_not_exists']['replace'] += 1;
                break;

            case 4:
                // product availability is null
                Session::removeProduct($key);
                list ($product_id, $feature_ids) = explode('|', trim($key, '|'));
                $product  = Product::get($product_id);
                $label    = strtr($Addon->i18n('error.cart_product_not_available'), ['{{replace}}' => $product->getValue('name_' . $lang_id)]);
                $errors[] = ['label' => $label];
                break;

            case 5:
                // not enough products
                $product = Product::getProductByKey($key, 0);
                // update cart
                Session::setProductQuantity($key, $product->getValue('amount'));
                $label    = strtr($Addon->i18n('error.cart_product_not_enough_amount'), [
                    '{{replace}}' => $product->getValue('name_' . $lang_id),
                    '{{count}}'   => $product->getValue('amount'),
                ]);
                $errors[] = ['label' => $label];
                break;

            default:
                throw new ProductException($msg, $ex->getCode());
                break;
        }
    }
}
while ($retry);

if (count($errors)):
    foreach ($errors as $error): ?>
        <div class="callout alert">
            <p><?= isset($error['replace']) ? strtr($error['label'], ['{{replace}}' => $error['replace']]) : $error['label'] ?></p>
        </div>
    <?php endforeach;
endif; ?>

<?php if (count($products)): ?>
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
        foreach ($products as $product)
        {
            $this->setVar('product', $product);
            echo $this->subfragment('product/general/cart/item.php');
        }
        ?>
        </tbody>
    </table>
</form>
<?php else: ?>
    <p class="text-center margin"><?= $Addon->i18n('label.no_product_in_cart') ?></p>
<?php endif; ?>
