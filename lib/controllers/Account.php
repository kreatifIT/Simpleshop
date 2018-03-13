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


class AccountController extends Controller
{
    protected $products = [];

    public function _execute()
    {
        $errors       = [];
        $this->params = array_merge([
            'content' => true,
        ], $this->params);

        foreach ($this->params as $key => $value) {
            $this->setVar($key, $value, false);
        }

        if (!Customer::isLoggedIn()) {
            $this->fragment_path[] = 'simpleshop/customer/auth/login.php';
            return $this;
        }

        $this->setVar('User', Customer::getCurrentUser());

        switch ($this->params['content']) {
            case 'dashboard':
                $this->setVar('template', 'dashboard.php');
                break;
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
        $this->fragment_path[] = 'simpleshop/customer/customer_area/wrapper.php';
    }
}