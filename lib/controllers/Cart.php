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
    protected $products = [];

    public function _execute()
    {
        $this->params = array_merge([
            'check_cart' => true,
        ], $this->params);

        $errors = [];

        try {
            $this->products = Session::getCartItems(false, $this->params['check_cart']);
        }
        catch (CartException $ex) {
            if ($ex->getCode() == 1) {
                $errors = Session::$errors;
            }
            $this->products = Session::getCartItems();
        }

        if (count($errors)) {
            ob_start();
            foreach ($errors as $error): ?>
                <div class="callout alert">
                    <p><?= isset($error['replace']) ? strtr($error['label'], ['{{replace}}' => $error['replace']]) : $error['label'] ?></p>
                </div>
            <?php endforeach;
            $this->output .= ob_get_clean();
        }

        foreach ($this->params as $key => $value) {
            $this->setVar($key, $value);
        }

        if (count($this->products)) {
            $this->setVar('products', $this->products);
            $this->fragment_path[] = 'simpleshop/cart/table-wrapper.php';
        }
        else {
            $this->fragment_path[] = 'simpleshop/cart/empty.php';
        }
    }

    public function getProducts()
    {
        return $this->products;
    }

    public static function ext_project_layoutBottom(\rex_extension_point $ep) {
        $subject = $ep->getSubject();
        $fragment = new \rex_fragment();
        $subject .= $fragment->parse('simpleshop/cart/offcanvas.php');
        return $subject;
    }
}