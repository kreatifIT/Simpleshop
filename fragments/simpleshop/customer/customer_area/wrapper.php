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

namespace FriendsOfREDAXO\Simpleshop;

$_FUNC = rex_request('action', 'string');
$path  = $this->getVar('fragment_path');

if ($_FUNC == 'logout')
{
    \FriendsOfREDAXO\Simpleshop\Customer::logout();
    header('Location: ' . rex_server('HTTP_REFERER', 'string'));
    exit;
}
else if (\FriendsOfREDAXO\Simpleshop\Customer::isLoggedIn())
{
    echo $this->subfragment($path);
}
else
{
    // show login form
    $this->setVar('class', 'row margin-large-bottom');
    echo $this->subfragment('simpleshop/customer/auth/login_wrapper.php');
}