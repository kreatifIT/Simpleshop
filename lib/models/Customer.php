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
    const MIN_PWD_LEN = 6;


    public static function getUserByEmail($email)
    {
        return self::query()->where('email', $email)->findOne();
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
        if (!empty($_SESSION['customer']['user']) && (int) $_SESSION['customer']['user']['id'] > 0) {
            $result = parent::get($_SESSION['customer']['user']['id']);

            if (!$result) {
                self::logout();
                rex_redirect(\rex_article::getCurrentId());
            }
        }
        return $result;
    }

    public function getName($lang_id = null)
    {
        if ($this->getValue('ctype') == 'company') {
            $name = $this->getValue('company_name');
        }
        else {
            $name = $this->getValue('firstname') . ' ' . $this->getValue('lastname');
        }
        return $name;
    }

    public static function register($email, $password, $attributes = [])
    {
        $result = null;
        $User   = self::getUserByEmail($email);

        // verify if a user with the given email already exist
        if ($User) {
            throw new CustomerException("User with given email already exist", 1);
        }
        // verify the password length
        else if (self::MIN_PWD_LEN > strlen($password)) {
            throw new CustomerException("Password must have at least " . self::MIN_PWD_LEN . " characters", 2);
        }
        else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new CustomerException("Email not valid", 3);
        }
        $_this = parent::create();

        foreach ($attributes as $attr => $value) {
            $_this->setValue($attr, $value);
        }
        $_this->setValue('email', $email);
        $_this->setValue('password', $password);
        $_this->setValue('status', 1);
        $_this->setValue('created', date('Y-m-d H:i:s'));
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
            $Mail          = new Mail();
            $do_send       = true;
            $Mail->Subject = '###shop.email.user_registration_subject###';
            $Mail->setFragmentPath('customer/registration');

            // add vars
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
            $result = Customer::get($_this->getId());
        }
        return $result;
    }

    public static function login($email, $password)
    {
        $result = null;
        $user   = self::getUserByEmail($email);

        if ($user && $user->isActive()) {
            $pwd = $user->getValue('password_hash');
            // prevent login for empty passwords!
            // and verify hash
            if (strlen($pwd) >= self::MIN_PWD_LEN && self::getPasswordHash($password) == $pwd) {
                // logged in
                $_SESSION['customer']['user'] = ['id' => $user->getValue('id')];
                // update login timestamp
                $user->setValue('lastlogin', date('Y-m-d H:i:s'));
                $user->setValue('lang_id', \rex_clang::getCurrentId());
                $user->save();

                $result = \rex_extension::registerPoint(new \rex_extension_point('Customer.logged_in', $user, [
                    'password' => $password,
                ]));
            }
        }
        // cleanup sessions
        Session::cleanupSessions();
        return $result;
    }

    public static function resetPassword($email)
    {
        $password = null;
        $User     = self::getUserByEmail($email);

        if ($User) {
            $password = random_string(self::MIN_PWD_LEN);
            $User->setValue('password', $password)->save();

            $Mail          = new Mail();
            $do_send       = true;
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

            if ($do_send) {
                $Mail->send();
            }
        }
        return $password;
    }

    public static function logout()
    {
        $_SESSION['customer']['user'] = [];
        session_regenerate_id(true);
        session_destroy();
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

    public static function getAccountFieldForm(\rex_yform $form, $Customer = null, $type = 'account', $additionalFields = [])
    {
        $includedFields = [];
        $excludesFields = ['lang_id', 'addresses', 'status', 'lastlogin', 'created', 'updatedate'];

        if ($type == 'account') {
            $excludesFields = array_merge($excludesFields, $additionalFields, ['password']);
        }
        else if ($type == 'registration') {
            $includedFields = array_merge(['firstname', 'lastname', 'email', 'password'], $additionalFields);
        }
        else if ($type == 'invoice') {
            $includedFields = array_merge($includedFields, ['ctype', 'company_name', 'firstname', 'lastname', 'fiscal_code', 'vat_num']);
        }
        $fields = Customer::getAllYformFields();
        $config = FragmentConfig::getValue('customer.css_class');

        $form->setObjectparams('main_table', Customer::TABLE);
        $form->setObjectparams('submit_btn_show', false);
        $form->setObjectparams('real_field_names', false);
        $form->setObjectparams('form_ytemplate', 'custom,foundation,bootstrap');
        $form->setObjectparams('error_class', 'form-warning');
        $form->setObjectparams('form_showformafterupdate', true);

        if ($Customer) {
            $form->setObjectparams('main_id', $Customer->getId());
            $form->setObjectparams('main_where', "id = {$Customer->getId()}");
            $form->setObjectparams('getdata', true);
        }
        return self::getPrepareFieldForm($form, $fields, $config, $excludesFields, $includedFields);
    }

    public static function getAddressFieldForm(\rex_yform $form, $customerId, $Address = null, $_excludesFields = [])
    {
        $excludesFields = array_merge($_excludesFields, ['customer_id', 'status']);

        $fields = CustomerAddress::getAllYformFields();
        $config = FragmentConfig::getValue('customer.css_class');

        $form->setObjectparams('main_table', CustomerAddress::TABLE);
        $form->setObjectparams('submit_btn_show', false);
        $form->setObjectparams('real_field_names', false);
        $form->setObjectparams('form_ytemplate', 'custom,foundation,bootstrap');
        $form->setObjectparams('error_class', 'form-warning');
        $form->setObjectparams('form_showformafterupdate', true);

        $form->setValueField('hidden', ['customer_id', $customerId]);

        if ($Address) {
            $form->setObjectparams('main_id', $Address->getId());
            $form->setObjectparams('main_where', "id = {$Address->getId()}");
            $form->setObjectparams('getdata', true);
            $form->setActionField('db', [CustomerAddress::TABLE, "id = {$Address->getId()}"]);
        }
        return self::getPrepareFieldForm($form, $fields, $config, $excludesFields);
    }

    private static function getPrepareFieldForm($form, $fields, $config, $excludesFields = [], $includedFields = [])
    {
        foreach ($fields as $index => $field) {
            // exclude types
            if ($field->getElement('type_name') == 'hidden_field' || in_array($field->getElement('name'), $excludesFields)) {
                continue;
            }
            else if (count($includedFields) && !in_array($field->getElement('name'), $includedFields)) {
                continue;
            }

            if ($field->getElement('type_id') == 'value') {
                $params = array_merge($field->toArray(), [
                    'css_class' => 'column ' . $config['form_fields'],
                ]);

                if ($field->getElement('type_name') == 'be_manager_relation') {
                    $params['field'] = strtr($params['field'], ['_1' => '_' . \rex_clang::getCurrentId()]);
                }
                $form->setValueField($field->getElement('type_name'), $params);
            }
            else if ($field->getElement('type_id') == 'validate') {
                $form->setValidateField($field->getElement('type_name'), $field->toArray());
            }
        }
        return $form;
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