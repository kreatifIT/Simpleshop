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

class CustomerAddress extends \rex_yform_manager_dataset
{
    const TABLE = 'rex_shop_customer_address';

    public static function action__save_checkout_address($action)
    {
        $addresses  = [];
        $CAddresses = [];
        $extras     = [];
        $values     = $action->getParam('value_pool');
        $hidden     = $action->getParam('form_hiddenfields');
        $user_id    = $hidden['customer_id'] ?: 0;

        foreach ($values['sql'] as $name => $value)
        {
            list ($key, $index, $_name) = explode('.', $name);

            if (strlen($name) && $key == 'customer_address')
            {
                $addresses[$index][$_name] = $value;
            }
            else if (strlen($name))
            {
                $extras[$name] = $value;
            }
        }

        foreach ($addresses as $index => $address)
        {
            $_this = (int) $hidden['id_' . $index] ? self::get($hidden['id_' . $index]) : self::create();
            $_this->setValue('customer_id', $user_id);

            foreach ($address as $name => $value)
            {
                $_this->setValue($name, $value);
            }
            $_this = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.CustomerAddress.preSaveAddress', $_this, [
                'hidden'  => $hidden,
                'values'  => $values,
                'extras'  => $extras,
                'address' => $address,
                'user_id' => $user_id,
            ]));
            if ($user_id)
            {
                // just save for registered user
                $_this->save();
            }
            $CAddresses[$index] = $_this;
        }
        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.CustomerAddress.addresses_saved', $CAddresses, [
            'hidden'  => $hidden,
            'values'  => $values,
            'extras'  => $extras,
            'user_id' => $user_id,
        ]));
    }
}