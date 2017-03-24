<?php

/**
 * This file is part of the FriendsOfREDAXO\Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 * Date: 23.03.17
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;


use Sprog\Wildcard;
use Whoops\Exception\ErrorException;

class CheckoutController extends Controller
{
    protected $products = [];

    protected function _execute()
    {
        $this->products = Session::getCartItems(true);

        $this->verifyParams(['cart_page_id', 'action']);

        if (count($this->products)) {
            switch ($this->params['action']) {
                case 'show-summary':
                    return $this->getSummaryView();
                    break;
            }
        }
        else {
            // no products - redirect to shopping cart
            rex_redirect($this->params['cart_page_id']);
        }
    }

    protected function verifyParams($required_params)
    {
        // verifiy params
        foreach ($required_params as $param) {
            if (!$this->params[$param]) {
                throw new ErrorException("The param '{$param}' is missing!");
            }
        }
    }

    protected function getSummaryView()
    {
        $errors   = [];
        $warnings = [];
        $Order    = Session::getCurrentOrder();

        try {
            $warnings = $Order->calculateDocument();
        }
        catch (OrderException $ex) {
            $errors[] = $ex->getMessage();
        }

        // verify product existance
        $product_cnt = count(Session::getCartItems(true));

        if ($product_cnt < 1) {
            $errors[] = Wildcard::get('shop.error_summary_no_product_available');
        }

        $this->fragment_path = 'simpleshop/checkout/summary/wrapper.php';
        $this->setVar('Order', $Order);
        $this->setVar('errors', $errors);
        $this->setVar('warnings', $warnings);
        $this->setVar('cart_url', rex_getUrl($this->params['cart_page_id']));
    }
}