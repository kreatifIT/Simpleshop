<?php

$products = \FriendsOfREDAXO\Simpleshop\Session::getCartItems();

?>
<div id="cart" class="section-wrapper margin-large-top margin-large-bottom">
    <div class="row">
        <div class="column">
            <?php
            $Controller = \FriendsOfREDAXO\Simpleshop\CartController::execute([
                'config'   => ['has_image' => true],
                'products' => $products,
            ]);
            echo $Controller->parse();
            ?>
            <a href="#" class="button">###action.go_ahead###</a>
        </div>
    </div>
</div>