<?php

$products = \FriendsOfREDAXO\Simpleshop\Session::getCartItems();

?>
<div class="row column margin-large-top margin-large-bottom">
    <h2 class="heading large separator">###label.cart###</h2>

    <div class="row">
        <div class="column cart-container">
            <?php
            $Controller = \FriendsOfREDAXO\Simpleshop\CartController::execute([
                'products' => $products,
            ]);
            echo $Controller->parse();
            ?>
        </div>
    </div>
</div>
