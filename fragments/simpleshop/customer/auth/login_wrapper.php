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

$_FUNC     = rex_request('action', 'string');
$sub_class = strlen($this->getVar('sub_class')) ? $this->getVar('sub_class') : 'large-6 columns margin-bottom';
$before    = $this->getVar('before');
$after     = $this->getVar('after');

if ($_FUNC == 'login')
{
    $email    = rex_post('email', 'string');
    $pwd      = rex_post('password', 'string');
    $redirect = from_array($_SESSION, 'redirect', rex_server('HTTP_REFERER', 'string'));
    $User     = \FriendsOfREDAXO\Simpleshop\Customer::login($email, $pwd);

    if (!isset($_SESSION['redirect']))
    {
        $_SESSION['redirect'] = $redirect;
    }
    if (!$User)
    {
        $this->setVar('login_errors', [$this->i18n('error.login_failed')]);
    }
    else if ($_SESSION['redirect'])
    {
        $redirect = $_SESSION['redirect'];
        unset($_SESSION['redirect']);
        header('Location: ' . $redirect);
        exit;
    }
}
?>
<div class="<?= $this->getVar('class') ?>">

    <?= $before ?>

    <?php
    $this->setVar('class', $sub_class);
    echo $this->subfragment('simpleshop/customer/auth/login.php');
    ?>

    <?php
    $this->setVar('class', $sub_class);
    echo $this->subfragment('simpleshop/customer/auth/registration.php');
    ?>

    <?= $after ?>
</div>