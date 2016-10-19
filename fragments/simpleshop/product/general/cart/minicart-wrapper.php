<div id="mini-cart">
    <span class="mini-cart-title">###label.cart###</span>
    <div class="mini-cart-inner">
        <table class="cart-content"></table>
        <div class="total">###label.total###: <span></span></div>
        <div class="mini-cart-buttons">
            <a href="<?= rex_getUrl(\Kreatif\Project\Settings::CART_PAGE_ID) ?>" class="button button-gray">###action.go_to_cart###</a>
            <a href="<?= rex_getUrl(\Kreatif\Project\Settings::CHECKOUT_PAGE_ID) ?>" class="button button-checkout">###action.go_to_checkout###</a>
        </div>
    </div>
    <div class="no-products">###shop.no_products_in_cart###</div>
</div>