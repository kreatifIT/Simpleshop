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
        $errors   = [];
        $Settings = \rex::getConfig('simpleshop.Settings');

        $this->params = array_merge([
            'controller' => '',
        ], $this->params);

        list($this->params['area'], $this->params['method']) = explode('.', $this->params['controller']);

        foreach ($this->params as $key => $value) {
            $this->setVar($key, $value, false);
        }

        // CHECK CUSTOMER IS LOGGED
        if (!Customer::isLoggedIn()) {
            $this->fragment_path[] = 'simpleshop/customer/auth/login.php';
            return $this;
        }


        // CHECK CONTENT IS ENABLED BY SETTINGS
        if (!in_array($this->params['area'], $Settings['membera_area_contents'])) {
            $this->fragment_path[] = 'simpleshop/customer/auth/no_permission.php';
            return $this;
        }

        $User = Customer::getCurrentUser();

        // CHECK USER HAS PERMISSION
        if (!$User->hasPermission("AccountController.content--{$this->params['area']}")) {
            $this->fragment_path[] = 'simpleshop/customer/auth/no_permission.php';
            return $this;
        }

        $this->setVar('User', $User);
        $this->setVar('template', "{$this->params['area']}.php");

        if (count($errors)) {
            $this->errors = array_merge($this->errors, $errors);
        }
        $this->fragment_path[] = 'simpleshop/customer/customer_area/wrapper.php';
    }
}