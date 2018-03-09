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
    protected $published = true;
    protected $success   = true;

    public function execute()
    {
        $lang_id = rex_request('lang', 'int');
        if ($lang_id) {
            setlocale(LC_ALL, rex_clang::get($lang_id)->getValue('clang_setlocale'));
        }

        $controller  = rex_request('controller', 'string', null);
        $_controller = 'api__' . strtr($controller, ['.' => '_']);

        if (!$controller || !method_exists($this, $_controller)) {
            throw new ApiException("Controller '{$controller}' doesn't exist");
        }
        try {
            $this->$_controller();
        }
        catch (ErrorException $ex) {
            throw new ApiException($ex->getMessage());
        }
        $this->response['controller'] = strtolower($controller);
        return new rex_api_result($this->success, $this->response);
    }

    private function api__cart_getcartcontent()
    {
        $products = \FriendsOfREDAXO\Simpleshop\Session::getCartItems(false, false);
        $result   = [
            'total' => 0,
            'html'  => '',
            'count' => count($products),
        ];

        if (count($products) == 0) {
            $fragment = new rex_fragment();
            $result['html'] = $fragment->parse('simpleshop/cart/empty.php');
        }
        $result = rex_extension::registerPoint(new rex_extension_point('Api.Cart.getCartContent', $result, ['products' => $products]));

        $this->response['cart_html']      = $result['html'];
        $this->response['total']          = $result['total'];
        $this->response['count']          = $result['count'];
        $this->response['total_formated'] = format_price($result['total']);
    }

    private function api__cart_addgiftcard()
    {
        $product_key = rex_post('product_key', 'string', null);
        $extras      = rex_post('extras', 'array', []);
        $fragment    = new rex_fragment();

        if (!$product_key) {
            throw new ApiException("Invalid request arguments");
        }

        try {
            rex_extension::registerPoint(new rex_extension_point('Api.Cart.addGiftcard.PRE_ADD', $product_key, ['extras' => $extras]));

            \FriendsOfREDAXO\Simpleshop\Session::setProductData($product_key, 1, $extras);
        }
        catch (ApiException $ex) {
            $this->success = false;
        }
        $fragment->setVar('product', \FriendsOfREDAXO\Simpleshop\Product::getProductByKey($product_key, $this->response['cart_items'][$product_key]['quantity'], $extras));
        $this->response['cart_item_html'] = $fragment->parse('simpleshop/cart/item.php');
    }

    private function api__cart_addproduct()
    {
        $product_key = rex_post('product_key', 'string', null);
        $quantity    = rex_post('quantity', 'int', 1);
        $exact_qty   = rex_post('exact_qty', 'int', 0);
        $extras      = rex_post('extras', 'array', []);
        $fragment    = new rex_fragment();

        if (!$product_key || ($quantity < 1 && $exact_qty < 1)) {
            throw new ApiException("Invalid request arguments");
        }

        if ($exact_qty) {
            \FriendsOfREDAXO\Simpleshop\Session::setProductData($product_key, $exact_qty, $extras);
        }
        else {
            \FriendsOfREDAXO\Simpleshop\Session::addProduct($product_key, $quantity, $extras);
        }
        $this->response['cart_items']    = \FriendsOfREDAXO\Simpleshop\Session::getCartItems(true);
        $this->response['cart_item_cnt'] = count($this->response['cart_items']);

        $fragment->setVar('product', \FriendsOfREDAXO\Simpleshop\Product::getProductByKey($product_key, $this->response['cart_items'][$product_key]['quantity'], $extras));
        $this->response['cart_item_html'] = $fragment->parse('simpleshop/cart/item.php');
    }

    private function api__cart_setproductquantity()
    {
        $product_key = rex_post('product_key', 'string', null);
        $quantity    = rex_post('quantity', 'int');
        $fragment    = new rex_fragment();

        if (!$product_key || $quantity < 1) {
            throw new ApiException("Invalid request arguments");
        }
        \FriendsOfREDAXO\Simpleshop\Session::setProductData($product_key, $quantity);

        $fragment->setVar('product', \FriendsOfREDAXO\Simpleshop\Product::getProductByKey($product_key, $this->response['cart_items'][$product_key]['quantity'], $extras));
        $this->response['cart_item_html'] = $fragment->parse('simpleshop/cart/item.php');
    }

    private function api__cart_removeproduct()
    {
        $product_key = rex_post('product_key', 'string', null);

        if (!$product_key) {
            throw new ApiException("Invalid request arguments");
        }
        \FriendsOfREDAXO\Simpleshop\Session::removeProduct($product_key);
        $this->api__cart_getcartcontent();
    }
}

class ApiException extends rex_api_exception
{
    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, $previous);
        rex_logger::logException($this);
    }

    public function getLabelByCode() { }
}