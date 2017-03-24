<?php

$products = \FriendsOfREDAXO\Simpleshop\Session::getCartItems(true);
$fragment = new rex_fragment();

?>
<div id="cart" class="padding-top padding-large-bottom">
    <div class="row">
        <div class="column padding-top">
            <?php
            if (count($products)) {
                echo \FriendsOfREDAXO\Simpleshop\CartController::execute([
                    'config' => ['has_image' => true],
                ]);
            }
            else {
                echo $fragment->parse('simpleshop/cart/empty.php');
            }
            ?>
        </div>
        <a href="#" class="button">###action.go_ahead###</a>
    </div>
</div>