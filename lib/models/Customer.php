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
    const TABLE       = 'rex_shop_customer';
    const MIN_PWD_LEN = 8;


    public static function getUserByEmail($email)
    {
        $stmt = self::query();
        $stmt->whereRaw('email LIKE :email', ['email' => trim($email)]);
        $User = $stmt->findOne();
        return \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Customer.getUserByEmail', $User, ['uname' => $email]));
    }

    public function save($prepare = false)
    {
        $type   = $this->getId() ? 'update' : 'insert';
        $result = parent::save($prepare);
        $result = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Customer.preSave', $result, ['type' => $type, 'Customer' => $this]));
        return $result;
    }

    public static function getCurrentUser()
    {
        $result = null;
        if (!empty($_SESSION['customer']['user']) && (int)$_SESSION['customer']['user']['id'] > 0) {
            $result = parent::get($_SESSION['customer']['user']['id']);

            if (!$result) {
                self::logout();
                rex_redirect(\rex_article::getCurrentId(), null, ['ts' => time()]);
            }
        }
        return $result;
    }

    public function getName($lang_id = null, $companyFallback = false)
    {
        $address = $this->getAddress();
        return $address ? $address->getName($lang_id, $companyFallback) : '';
    }

    public function getAddress()
    {
        $addressId = $this->getValue('invoice_address_id');
        return $addressId ? CustomerAddress::get($addressId) : null;
    }

    public function hasPermission($permission)
    {
        return \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Customer.hasPermission', true, [
            'User'       => $this,
            'permission' => $permission,
        ]));
    }

    public static function activate($hash)
    {
        $isActivated = 0;
        $hash        = trim($hash);

        if ($hash != '') {
            $stmt = self::query();
            $stmt->where('hash', $hash);
            $customer = $stmt->findOne();

            if ($customer) {

                if ($customer->getValue('activationdate') == '0000-00-00 00:00:00') {
                    $sql = \rex_sql::factory();
                    $sql->setTable(self::TABLE);
                    $sql->setValue('status', 1);
                    $sql->setRawValue('activationdate', 'NOW()');
                    $sql->setWhere('id = :id', ['id' => $customer->getId()]);
                    $sql->update();
                    $isActivated = 1;
                } else {
                    $isActivated = 2;
                }
            }
        }
        return $isActivated;
    }

    public static function register($email, $password, $attributes = [], $User = null, $doubleOptIn = false)
    {
        $result      = null;
        $email       = trim(mb_strtolower($email));
        $doubelOptIn = FragmentConfig::$data['auth']['registration_doubleOptIn'];

        if ($User) {
            $_this = $User;
        } else {
            $User = self::getUserByEmail($email);

            // verify if a user with the given email already exist
            if ($User) {
                throw new CustomerException("User with given email already exist", 1);
            }
            $_this = parent::create();
        }
        foreach ($attributes as $attr => $value) {
            $_this->setValue($attr, trim($value));
        }

        $statusField = self::getYformFieldByName('status');
        $exclFields  = array_diff(FragmentConfig::getValue('yform_fields.rex_shop_customer._excludedFields'), ['created', 'updatedate', 'status']);

        FragmentConfig::$data['yform_fields']['rex_shop_customer']['_excludedFields'] = $exclFields;

        $_this->setValue('email', $email);
        $_this->setValue('hash', sha1($email . time()));
        $_this->setValue('password', $password);
        $_this->setValue('status', $doubelOptIn ? 0 : $statusField->getElement('default'));

        $success  = $_this->save();
        $messages = $_this->getMessages();

        if (count($messages)) {
            throw new CustomerException(implode('||', $messages), 99);
        }

        \rex_extension::registerPoint(new \rex_extension_point('Customer.registered', $success, [
            'user'     => $_this,
            'password' => $password,
        ]));

        if ($success) {
            // create customer address
            $address = CustomerAddress::create();

            foreach ($attributes as $attr => $value) {
                $address->setValue($attr, trim($value));
            }
            $address->save();

            $sql = \rex_sql::factory();
            $sql->setTable(CustomerAddress::TABLE);
            $sql->setValue('customer_id', $_this->getId());
            $sql->setValue('status', 1);
            $sql->setWhere('id = :id', ['id' => $address->getId()]);
            $sql->update();

            $sql->setTable(self::TABLE);
            $sql->setValue('invoice_address_id', $address->getId());
            $sql->setWhere('id = :id', ['id' => $_this->getId()]);
            $sql->update();

            $Mail          = new Mail();
            $do_send       = true;
            $Mail->Subject = '###label.email__user_registration_subject###';

            if ($doubelOptIn) {
                $Mail->setFragmentPath('simpleshop/email/customer/registration_activation.php');
            } else {
                $Mail->setFragmentPath('simpleshop/email/customer/registration.php');
            }

            // add vars
            $Mail->setVar('customer', $_this);
            $Mail->setVar('email', $email);
            $Mail->setVar('password', $password);
            $Mail->AddAddress($email);

            $do_send = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Customer.sendRegistrationEmail', $do_send, [
                'Mail'     => $Mail,
                'User'     => $_this,
                'password' => $password,
            ]));

            if ($do_send) {
                $Mail->send();
            }
            if (!$doubelOptIn) {
                $result = Customer::get($_this->getId());
            }
        }
        return $result;
    }

    public static function registerFromBeUser($email, $attributes = [])
    {
        $sql        = \rex_sql::factory();
        $password   = random_string(self::MIN_PWD_LEN);
        $attributes = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Customer.registerFromBeUser.attributes', array_merge([
            'status'     => 1,
            'created'    => date('Y-m-d H:i:s'),
            'updatedate' => date('Y-m-d H:i:s'),
        ], $attributes), [
            'email' => $email,
        ]));

        foreach ($attributes as $key => $value) {
            $sql->setValue($key, $value);
        }

        $sql->setTable(self::TABLE);
        $sql->setValue('email', $email);
        $sql->setWhere('email', $email);
        $sql->setValue('password_hash', self::getPasswordHash($password));
        $sql->insertOrUpdate();

        try {
            self::login($email, $password);
        } catch (CustomerException $ex) {
        }
    }

    public static function login($email, $password)
    {
        $result   = null;
        $email    = trim($email);
        $password = trim($password);

        if ($password == '' || $email == '') {
            return null;
        }

        $sql    = \rex_sql::factory();
        $user   = self::getUserByEmail($email);
        $query  = 'SELECT * FROM rex_user WHERE email LIKE :email AND status = 1';
        $beuser = $sql->getArray($query, ['email' => $email])[0];

        if ($beuser) {
            if (\rex::getUser()) {
                $loginCheck = true;
            } else {
                $login = new \rex_backend_login();
                $login->setLogin($beuser['login'], $password);
                $loginCheck = $login->checkLogin();
            }
        }


        if ($user && ($user->getValue('status') == 1 || $beuser)) {
            $pwd = $user->getValue('password_hash');

            // prevent login for empty passwords!
            // and verify hash
            if ((strlen($pwd) >= self::MIN_PWD_LEN && self::getPasswordHash($password) == $pwd) || $loginCheck) {
                // logged in
                $_SESSION['customer']['user'] = ['id' => $user->getValue('id')];
                // update login timestamp
                $sql->setTable(self::TABLE);
                $sql->setValue('lastlogin', date('Y-m-d H:i:s'));
                $sql->setValue('lang_id', \rex_clang::getCurrentId());
                $sql->setWhere('id', $user->getId());
                $sql->update();

                $result = \rex_extension::registerPoint(new \rex_extension_point('Customer.logged_in', $user, [
                    'password' => $password,
                ]));
            }
        } else if ($beuser && $loginCheck && $beuser['email']) {
            Customer::registerFromBeUser($beuser['email']);
        }
        // cleanup sessions
        Session::cleanupSessions();
        return $result;
    }

    public static function resetPassword($email)
    {
        $password = null;
        $User     = self::getUserByEmail($email);

        if ($User && $User->getValue('status') == 1) {
            $password = random_string(self::MIN_PWD_LEN);
            $sql      = \rex_sql::factory();
            $sql->setTable(self::TABLE);
            $sql->setValue('password_hash', self::getPasswordHash($password));
            $sql->setWhere('id = :id', ['id' => $User->getId()]);
            $sql->update();

            $Mail          = new Mail();
            $do_send       = true;
            $Mail->Subject = '###label.email__user_password_reset###';
            $Mail->setFragmentPath('simpleshop/email/customer/password_reset.php');

            // add vars
            $Mail->setVar('password', $password);
            $Mail->setVar('User', $User);
            $Mail->AddAddress($email);

            \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Customer.resetPasswordEmail', $do_send, [
                'Mail'     => $Mail,
                'User'     => $User,
                'password' => $password,
            ]));

            if ($do_send) {
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
        return (!empty($_SESSION['customer']['user']) && (int)$_SESSION['customer']['user']['id'] > 0);
    }
}

class CustomerException extends \Exception
{
    public function getLabelByCode()
    {
        switch ($this->getCode()) {
            case 1:
                $errors = ['###error.user_already_exist###'];
                break;
            case 2:
                $errors = [strtr(Wildcard::get('error.password_to_short'), ['%d' => Customer::MIN_PWD_LEN])];
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