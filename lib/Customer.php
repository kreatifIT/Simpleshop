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

class Customer extends \rex_yform_manager_dataset
{
    const TABLE = 'rex_customer';
    const MIN_PASSWORD_LENGTH = 6;

    public static function getUserByEmail($email)
    {
        return self::query()->where('email', $email)->findOne();
    }

    public static function getCurrentUser()
    {
        $result = NULL;
        if (!empty($_SESSION['customer']['user']) && (int) $_SESSION['customer']['user']['id'] > 0)
        {
            $result = parent::get($_SESSION['customer']['user']['id']);
        }
        return $result;
    }

    public static function register($email, $password, $attributes = [])
    {
        $result = NULL;
        $User   = self::getUserByEmail($email);

        // verify if a user with the given email already exist
        if ($User)
        {
            throw new \ErrorException("User with given email already exist", 1);
        }
        // verify the password length
        else if (self::MIN_PASSWORD_LENGTH > strlen($password))
        {
            throw new \ErrorException("Password must have at least " . self::MIN_PASSWORD_LENGTH . " characters", 2);
        }
        $_this = parent::create();

        foreach ($attributes as $attr => $value)
        {
            $_this->setValue($attr, $value);
        }
        $_this
            ->setValue('email', $email)
            ->setValue('password', $password)
            ->setValue('status', 1)
            ->setValue('created', date('Y-m-d H:i:s'));
        $success = $_this->save();

        if (\rex_extension::registerPoint(new \rex_extension_point('Customer.registered', $success, [
            'user'     => $_this,
            'password' => $password,
        ]))
        )
        {
            $result = $_this;
        }
        return $result;
    }

    public static function login($email, $password)
    {
        $result = NULL;
        $user   = self::getUserByEmail($email);

        if ($user && $user->isActive())
        {
            $pwd = $user->getValue('password_hash');
            // prevent login for empty passwords!
            // and verify hash
            if (strlen($pwd) >= self::MIN_PASSWORD_LENGTH && self::getPasswordHash($password) == $pwd)
            {
                // logged in
                $_SESSION['customer']['user'] = ['id' => $user->getValue('id')];
                // update login timestamp
                $user->setValue('lastlogin', date('Y-m-d H:i:s'))->save();

                $result = \rex_extension::registerPoint(new \rex_extension_point('Customer.logged_in', $user, [
                    'password' => $password,
                ]));
            }
        }
        if (!$result)
        {
            // login failed
            self::logout();
        }
        // cleanup sessions
        Session::cleanupSessions();
        return $result;
    }

    public static function resetPassword($email)
    {
        $password = NULL;
        $User     = self::getUserByEmail($email);

        if ($User)
        {
            $password = self::generateRandomString(self::MIN_PASSWORD_LENGTH);
            $User->setValue('password', $password)->save();
        }
        return $password;
    }

    public static function logout()
    {
        $_SESSION['customer']['user'] = [];
    }

    public static function getPasswordHash($password, $func = 'sha1')
    {
        $yform = yform::factory();
        $yform->
        pr($yform->getFieldValue('password_hash'));
        exit;
        return hash($func, $password . 'UYD7FFtMLdqr4ZujqwED');
    }

    public static function isLoggedIn()
    {
        return (!empty($_SESSION['customer']['user']) && (int) $_SESSION['customer']['user']['id'] > 0);
    }

    public function isActive()
    {
        return $this->getValue('status') == 1;
    }

    public static function generateRandomString($length, &$__string = '')
    {
        $string = $__string . substr(str_shuffle('abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 1);
        if (strlen($string) < $length)
        {
            $string = self::generateRandomString($length, $string);
        }
        return $string;
    }
}