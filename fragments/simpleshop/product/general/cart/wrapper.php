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

$_FUNC     = rex_request('func', 'string');
$checkCart = $this->getVar('check_cart', TRUE);

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

try
{
    $products = Session::getCartItems(FALSE, $checkCart);
}
catch (CartException $ex)
{
    if ($ex->getCode() == 1)
    {
        $errors   = Session::$errors;
        $products = Session::getCartItems();
    }
}

if (count($errors)):
    foreach ($errors as $error): ?>
        <div class="callout alert">
            <p><?= isset($error['replace']) ? strtr($error['label'], ['{{replace}}' => $error['replace']]) : $error['label'] ?></p>
        </div>
    <?php endforeach;
endif;
?>

<?php if (count($products)): ?>
    <form action="" method="post">
        <table class="cart-content stack">
            <thead>
            <tr class="cart-header">
                <th>###label.preview###</th>
                <th>###label.product###</th>
                <th>###label.price###</th>
                <th>###label.amount###</th>
                <th>###label.total###</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>

            <?php
            foreach ($products as $product)
            {
                $this->setVar('product', $product);
                echo $this->subfragment('simpleshop/product/general/cart/item.php');
            }
            ?>
            </tbody>
        </table>
    </form>
<?php else: ?>
    <p class="text-center margin">###label.no_product_in_cart###</p>
<?php endif; ?>
