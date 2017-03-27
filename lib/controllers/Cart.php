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


class CartController extends Controller
{

    public function _execute()
    {
        $this->params = array_merge([
            'check_cart' => true,
            'ahead_url'  => '',
            'products'   => [],
            'config'     => [],
        ], $this->params);

        $errors   = [];
        $products = $this->params['products'];
        $_func    = rex_request('func', 'string');


        if ($_func == 'update') {
            $__products = rex_post('quantity', 'array', []);
            foreach ($__products as $key => $quantity) {
                Session::setProductData($key, $quantity);
            }
        }
        else if ($_func == 'remove') {
            Session::removeProduct(rex_get('key'));
            header('Location: ' . rex_getUrl());
            exit();
        }

        if (count($products) == 0) {
            try {
                $products = Session::getCartItems(false, $this->params['check_cart']);
            }
            catch (CartException $ex) {
                if ($ex->getCode() == 1) {
                    $errors   = Session::$errors;
                    $products = Session::getCartItems();
                }
            }
        }

        if (count($errors)) {
            foreach ($errors as $error): ?>
                <div class="callout alert">
                    <p><?= isset($error['replace']) ? strtr($error['label'], ['{{replace}}' => $error['replace']]) : $error['label'] ?></p>
                </div>
            <?php endforeach;
        }

        foreach ($this->params as $key => $value) {
            $this->setVar($key, $value);
        }

        if (count($products)) {
            $this->setVar('products', $products);
            $this->fragment_path = 'simpleshop/cart/table-wrapper.php';
        }
        else {
            $this->fragment_path = 'simpleshop/cart/empty.php';
        }
    }
}