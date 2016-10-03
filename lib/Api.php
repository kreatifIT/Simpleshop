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
        $controller  = rex_request('controller', 'string', NULL);
        $_controller = 'api__' . strtr($controller, ['.' => '__']);

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


    private function api__session__getcarthtml()
    {
        $this->response['cart_html'] = \FriendsOfREDAXO\Simpleshop\Session::getCartItems(TRUE);
    }

    private function api__session__addproduct()
    {
        $product_key = rex_post('product_key', 'string', NULL);
        $quantity    = rex_post('quantity', 'int', 1);

        if (!$product_key || $quantity < 1)
        {
            throw new rex_api_exception("Invalid request arguments");
        }
        \FriendsOfREDAXO\Simpleshop\Session::addProduct($product_key, $quantity);
        $this->api__session__getcarthtml();
    }
    
    private function api__session__removeproduct()
    {
        $product_key = rex_post('product_key', 'string', NULL);

        if (!$product_key)
        {
            throw new rex_api_exception("Invalid request arguments");
        }
        \FriendsOfREDAXO\Simpleshop\Session::removeProduct($product_key);
        $this->api__session__getcarthtml();
    }
}