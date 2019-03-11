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

class Customer extends Model
{
    const TABLE               = 'rex_shop_customer';
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

    public function getName($lang_id = true)
    {
        $name = $this->getValue('firstname') .' '. $this->getValue('lastname');
        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Customer.getName', $name, ['Customer' => $this]));
        return $name;
    }

    public static function register($email, $password, $attributes = [])
    {
        $result = NULL;
        $User   = self::getUserByEmail($email);

        // verify if a user with the given email already exist
        if ($User)
        {
            throw new CustomerException("User with given email already exist", 1);
        }
        // verify the password length
        else if (self::MIN_PASSWORD_LENGTH > strlen($password))
        {
            throw new CustomerException("Password must have at least " . self::MIN_PASSWORD_LENGTH . " characters", 2);
        }
        else if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE)
        {
            throw new CustomerException("Email not valid", 3);
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
        $success  = $_this->save();
        $messages = $_this->getMessages();

        if (count($messages))
        {
            throw new CustomerException(implode('||', $messages), 99);
        }

        \rex_extension::registerPoint(new \rex_extension_point('Customer.registered', $success, [
            'user'     => $_this,
            'password' => $password,
        ]));

        if($success)
        {
            $Mail          = new Mail();
            $do_send       = TRUE;
            $Mail->Subject = '###shop.email.user_registration_subject###';
            $Mail->setFragmentPath('customer/registration');

            // add vars
            $Mail->setVar('email', $email);
            $Mail->setVar('password', $password);
            $Mail->AddAddress($email);

            \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Customer.sendRegistrationEmail', $do_send, [
                'Mail'     => $Mail,
                'User'     => $_this,
                'password' => $password,
            ]));

            if ($do_send)
            {
                $Mail->send();
            }
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
            $password = random_string(self::MIN_PASSWORD_LENGTH);
            $User->setValue('password', $password)->save();

            $Mail          = new Mail();
            $do_send       = TRUE;
            $Mail->Subject = '###shop.email.user_password_reset###';
            $Mail->setFragmentPath('customer/password_reset');

            // add vars
            $Mail->setVar('password', $password);
            $Mail->setVar('User', $User);
            $Mail->AddAddress($email);

            \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Customer.resetPasswordEmail', $do_send, [
                'Mail'     => $Mail,
                'User'     => $User,
                'password' => $password,
            ]));

            if ($do_send)
            {
                $Mail->send();
            }
        }
        return $password;
    }

    public static function logout()
    {
        $_SESSION['customer']['user'] = [];
    }

    public static function getPasswordHash($password, $func = 'sha1')
    {
        $table  = \rex_yform_manager_table::get(Customer::TABLE);
        $fields = $table->getFields(['name' => 'password_hash']);
        $salt   = isset ($fields[0]) ? $fields[0]->getElement('salt') : '';
        return hash($func, $password . $salt);
    }

    public static function isLoggedIn()
    {
        return (!empty($_SESSION['customer']['user']) && (int) $_SESSION['customer']['user']['id'] > 0);
    }

    public function isActive()
    {
        return $this->getValue('status') == 1;
    }
}

class CustomerException extends \Exception
{
    public function getLabelByCode()
    {
        switch ($this->getCode())
        {
            case 1:
                $errors = ['###error.user_already_exist###'];
                break;
            case 2:
                $errors = [strtr(Wildcard::get('error.password_to_short'), ['%d' => Customer::MIN_PASSWORD_LENGTH])];
                break;
            case 3:
                $errors = ['###error.email_not_valid###'];
                break;
            case 99:
                $errors = explode('||', $this->getMessage());
                break;
            default:
                $errors = [$this->getMessage()];
                break;
        }
        return $errors;
    }
}