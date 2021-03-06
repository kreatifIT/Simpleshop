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
        ob_start();

        $lang_id = rex_request('lang', 'int');
        if ($lang_id) {
            rex_clang::setCurrentId($lang_id);

            setlocale(LC_ALL, rex_clang::get($lang_id)
                ->getValue('clang_setlocale'));
        }

        $controller  = rex_request('controller', 'string', null);
        $_controller = 'api__' . strtr($controller, ['.' => '_']);

        if (!$controller || !method_exists($this, $_controller)) {
            throw new ApiException("Controller '{$controller}' doesn't exist");
        }
        try {
            $this->$_controller();
        } catch (ErrorException $ex) {
            throw new ApiException($ex->getMessage());
        }
        $this->response['controller'] = strtolower($controller);
        $this->response               = rex_extension::registerPoint(new rex_extension_point('simpleshop.Api.response', $this->response, []));

        rex_response::cleanOutputBuffers();
        return new rex_api_result($this->success, $this->response);
    }

    private function api__package_selectproducts()
    {
        $limit   = 6;
        $lang_id = rex_clang::getCurrentId();
        $result  = ['results' => [], 'pagination' => ['more' => false]];
        $page    = rex_get('page', 'int', 0);
        $term    = rex_get('term', 'string');
        $orderBy = rex_get('orderby', 'string', 'name_' . $lang_id);
        $offset  = $page * $limit;

        $stmt = \FriendsOfREDAXO\Simpleshop\Product::query();
        $stmt->whereRaw("(
            name_{$lang_id} LIKE :term
            OR code LIKE :term
        )", ['term' => "%{$term}%"]);
        $stmt->orderBy($orderBy);

        $totalCnt   = $stmt->count();
        $stmt->limit($offset, $limit);
        $collection = $stmt->find();

        foreach ($collection as $item) {
            $variants = $item->getFeatureVariants();

            if (count($variants['variants'])) {
                foreach ($variants['variants'] as $vitem) {
                    $_flabel    = [];
                    $featureIds = explode(',', $vitem->getValue('variant_key'));

                    foreach ($featureIds as $featureId) {
                        $_flabel[] = \FriendsOfREDAXO\Simpleshop\FeatureValue::get($featureId)
                            ->getName();
                    }
                    $result['results'][] = [
                        'id'   => $vitem->getId() . '|' . $vitem->getValue('variant_key'),
                        'name' => $vitem->getName(),
                        'text' => "[{$vitem->getValue('code')}]  {$vitem->getName()}  |  " . implode(' + ', $_flabel),
                    ];
                }
            } else {
                $result['results'][] = [
                    'id'   => $item->getId(),
                    'name' => $item->getName(),
                    'text' => "[{$item->getValue('code')}]  {$item->getName()}",
                ];
            }
        }
        $totalDiff = $totalCnt - ($offset + $limit);

        $result['pagination']['more'] = $totalDiff > 0;
        $this->response['result']     = $result;
    }

    private function api__cart_getcartcontent($layout)
    {
        $ctrlTpl    = $layout == 'offcanvas_cart' ? 'simpleshop/cart/offcanvas/container.php' : null;
        $wrapper    = rex_request('wrapper', 'string', null);
        $ctrlParams = [];

        if ($wrapper) {
            $ctrlParams['config']['wrapper'] = $wrapper;
        }
        $Controller = \FriendsOfREDAXO\Simpleshop\CartController::execute($ctrlParams);

        $Controller->setVar('product_added', true);

        $products = $Controller->getProducts();
        $result   = [
            'total' => 0,
            'html'  => $Controller->parse($ctrlTpl),
            'count' => count($products),
        ];
        $result   = rex_extension::registerPoint(new rex_extension_point('Api.Cart.getCartContent', $result, ['products' => $products]));

        $this->response['cart_html']       = \Sprog\Wildcard::parse($result['html']);
        $this->response['total']           = $result['total'];
        $this->response['count']           = $result['count'];
        $this->response['total_formatted'] = format_price(\FriendsOfREDAXO\Simpleshop\Session::getTotal());
    }

    private function api__cart_addgiftcard()
    {
        $product_key = rex_post('product_key', 'string', null);
        $extras      = rex_post('extras', 'array', []);

        if (!$product_key) {
            throw new ApiException("Invalid request arguments");
        }
        try {
            rex_extension::registerPoint(new rex_extension_point('Api.Cart.addGiftcard.PRE_ADD', $product_key, ['extras' => $extras]));

            \FriendsOfREDAXO\Simpleshop\Session::setProductData($product_key, 1, $extras);
        } catch (ApiException $ex) {
            $this->success = false;
        }
        $this->api__cart_getcartcontent();
    }

    private function api__cart_addproduct()
    {
        $product_key = rex_post('product_key', 'string', null);
        $layout      = rex_post('layout', 'string', 'cart');
        $quantity    = rex_post('quantity', 'int', 1);
        $exact_qty   = rex_post('exact_qty', 'int', 0);
        $extras      = rex_post('extras', 'array', []);

        if (!$product_key || ($quantity < 1 && $exact_qty < 1)) {
            throw new ApiException("Invalid request arguments");
        }

        rex_extension::registerPoint(new rex_extension_point('Api.Cart.BeforeChangeAmoutProduct', $product_key,[]));

        if ($exact_qty) {
            \FriendsOfREDAXO\Simpleshop\Session::setProductData($product_key, $exact_qty, $extras);
        } else {
            \FriendsOfREDAXO\Simpleshop\Session::addProduct($product_key, $quantity, $extras);
        }
        $this->response['cart_items']       = \FriendsOfREDAXO\Simpleshop\Session::getCartItems(true);
        $this->response['cart_product_cnt'] = count($this->response['cart_items']);
        $this->response['cart_item_cnt']    = \FriendsOfREDAXO\Simpleshop\Session::getCartItemCount();

        $this->api__cart_getcartcontent($layout);

        $this->response = rex_extension::registerPoint(new rex_extension_point('Api.Cart.AddProductResponse', $this->response,[]));
    }

    private function api__cart_setproductquantity()
    {
        $layout      = rex_post('layout', 'string', 'cart');
        $product_key = rex_post('product_key', 'string', null);
        $quantity    = rex_post('quantity', 'int');

        if (!$product_key || $quantity < 1) {
            throw new ApiException("Invalid request arguments");
        }
        \FriendsOfREDAXO\Simpleshop\Session::setProductData($product_key, $quantity);

        $this->api__cart_getcartcontent($layout);
    }

    private function api__cart_refresh()
    {
        $layout = rex_post('layout', 'string', 'cart');
        $this->api__cart_getcartcontent($layout);
    }

    private function api__cart_removeproduct()
    {
        $layout      = rex_post('layout', 'string', 'cart');
        $product_key = rex_post('product_key', 'string', null);

        if (!$product_key) {
            throw new ApiException("Invalid request arguments");
        }
        $product_key = rex_extension::registerPoint(new rex_extension_point('Api.Cart.BeforeRemoveProduct', $product_key,[]));

        \FriendsOfREDAXO\Simpleshop\Session::removeProduct($product_key);

        $this->response['cart_items']       = \FriendsOfREDAXO\Simpleshop\Session::getCartItems(true);
        $this->response['cart_product_cnt'] = count($this->response['cart_items']);
        $this->response['cart_item_cnt']    = \FriendsOfREDAXO\Simpleshop\Session::getCartItemCount();

        $this->api__cart_getcartcontent($layout);

        $this->response = rex_extension::registerPoint(new rex_extension_point('Api.Cart.RemoveProductResponse', $this->response,[]));
    }

    private function api__custom_callback()
    {
        $callback = trim(rex_request('callback', 'string'));

        if ($callback == '') {
            throw new ApiException("Callback is not set");
        }
        $this->response = array_merge(call_user_func($callback), $this->response);
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