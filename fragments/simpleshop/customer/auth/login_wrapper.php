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

use Sprog\Wildcard;

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
        $this->setVar('login_errors', ['###error.login_failed###']);
    }
    else if ($_SESSION['redirect'])
    {
        $redirect = $_SESSION['redirect'];
        unset($_SESSION['redirect']);
        header('Location: ' . $redirect);
        exit;
    }
}
else if ($_FUNC == 'password-reset')
{
    $email = rex_post('email', 'string');
    $this->setVar('success', Customer::resetPassword($email));
}
else if ($_FUNC == 'registration')
{
    $email    = rex_post('email', 'string');
    $pwd      = random_string(Customer::MIN_PASSWORD_LENGTH);
    $redirect = from_array($_SESSION, 'redirect', rex_server('HTTP_REFERER', 'string'));

    try
    {
        $User = Customer::register($email, $pwd, [
            'firstname' => rex_post('firstname', 'string'),
            'firstname' => rex_post('lastname', 'string'),
        ]);
    }
    catch (CustomerException $ex)
    {
        $errors = $ex->getLabelByCode();
        $this->setVar('registration_errors', $errors);
    }

    if (!isset($errors))
    {
        if (!isset($_SESSION['redirect']))
        {
            $_SESSION['redirect'] = $redirect;
        }
        if (!$User)
        {
            $this->setVar('registration_errors', ['###error.registration_failed###']);
        }
        else
        {
            $User = \FriendsOfREDAXO\Simpleshop\Customer::login($email, $pwd);

            if ($User && $_SESSION['redirect'])
            {
                $redirect = $_SESSION['redirect'];
                unset($_SESSION['redirect']);
                header('Location: ' . $redirect);
                exit;
            }
        }
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