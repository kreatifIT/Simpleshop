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
class rex_api_simpleshop_api extends rex_api_function
{
    protected $response  = [];
    protected $published = TRUE;

    public function execute()
    {
        $lang_id = rex_request('lang', 'int');
        setlocale(LC_ALL, rex_clang::get($lang_id)->getValue('clang_setlocale'));

        $controller  = rex_request('controller', 'string', NULL);
        $_controller = 'api__' . strtr($controller, ['.' => '_']);

        if (!$controller || !method_exists($this, $_controller))
        {
            throw new rex_api_exception("Controller '{$controller}' doesn't exist");
        }
        try
        {
            $this->$_controller();
        }
        catch (ErrorException $ex)
        {
            throw new rex_api_exception($ex->getMessage());
        }
        $this->response['controller'] = strtolower($controller);
        return new rex_api_result(TRUE, $this->response);
    }

    private function api__cart_getpopupcontent()
    {
        $product_key = rex_post('product_key', 'string', NULL);
        $product     = \FriendsOfREDAXO\Simpleshop\Product::getProductByKey($product_key);

        if (!$product)
        {
            throw new rex_api_exception("No Product found for key = " . $product_key);
        }
        $fragment = new rex_fragment();
        $fragment->setVar('product', $product);
        $html                         = $fragment->parse('simpleshop/product/general/cart/popup.php');
        $this->response['popup_html'] = rex_extension::registerPoint(new rex_extension_point('Api.Cart.getPopupContent', $html, ['product' => $product]));
    }

    private function api__cart_getcartcontent()
    {
        $products = \FriendsOfREDAXO\Simpleshop\Session::getCartItems(FALSE, FALSE);
        $result   = [
            'total' => 0,
            'html'   => '',
            'count'  => count($products),
        ];

        foreach ($products as $product)
        {
            $result['total'] += $product->getPrice(TRUE) * $product->getValue('cart_quantity');
            $fragment = new rex_fragment();
            $fragment->setVar('product', $product);
            $fragment->setVar('class', 'cart-item-preview');
            $fragment->setVar('has_quantity_control', FALSE);
            $fragment->setVar('has_remove_button', FALSE);
            $result['html'] .= $fragment->parse('simpleshop/product/general/cart/item.php');
        }
        $result = rex_extension::registerPoint(new rex_extension_point('Api.Cart.getCartContent', $result, ['products' => $products]));

        $this->response['cart_html']       = $result['html'];
        $this->response['total']           = $result['total'];
        $this->response['count']           = $result['count'];
        $this->response['total_formated'] = format_price($result['total']);
    }

    private function api__cart_addgiftcard()
    {
        $product_key = rex_post('product_key', 'string', NULL);
        $extras      = rex_post('extras', 'array', []);

        if (!$product_key)
        {
            throw new rex_api_exception("Invalid request arguments");
        }

        try
        {
            rex_extension::registerPoint(new rex_extension_point('Api.Cart.addGiftcard.PRE_ADD', $product_key, ['extras' => $extras]));

            \FriendsOfREDAXO\Simpleshop\Session::setProductData($product_key, 1, $extras);
            $this->api__cart_getpopupcontent();
        }
        catch (ApiExtension $ex)
        {
            $this->getErrorPopup($ex->getLabelByCode());
        }
    }

    private function api__cart_addproduct()
    {
        $product_key = rex_post('product_key', 'string', NULL);
        $quantity    = rex_post('quantity', 'int', 1);
        $extras      = rex_post('extras', 'array', []);

        if (!$product_key || $quantity < 1)
        {
            throw new rex_api_exception("Invalid request arguments");
        }

        \FriendsOfREDAXO\Simpleshop\Session::addProduct($product_key, $quantity, $extras);
        $this->api__cart_getpopupcontent();
    }

    private function api__cart_setproductquantity()
    {
        $product_key = rex_post('product_key', 'string', NULL);
        $quantity    = rex_post('quantity', 'int');

        if (!$product_key || $quantity < 1)
        {
            throw new rex_api_exception("Invalid request arguments");
        }
        \FriendsOfREDAXO\Simpleshop\Session::setProductData($product_key, $quantity);
        $this->api__cart_getpopupcontent();
    }

    private function api__cart_removeproduct()
    {
        $product_key = rex_post('product_key', 'string', NULL);

        if (!$product_key)
        {
            throw new rex_api_exception("Invalid request arguments");
        }
        \FriendsOfREDAXO\Simpleshop\Session::removeProduct($product_key);
        $this->api__cart_getcartcontent();
    }

    private function getErrorPopup($message)
    {
        $fragment = new rex_fragment();
        $fragment->setVar('message', $message);
        $this->response['popup_html'] = $fragment->parse('simpleshop/product/general/cart/popup_error.php');
    }
}

class ApiExtension extends Exception {
    public function getLabelByCode() {}
}