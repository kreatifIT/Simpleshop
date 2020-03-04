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
class rex_yform_value_list_name extends rex_yform_value_abstract
{

    public function enterObject()
    {
    }

    public static function getListValue($params)
    {
        $result = '-';
        $table  = rex_get('table_name', 'string');

        if ($table == \FriendsOfREDAXO\Simpleshop\CustomerAddress::TABLE) {
            $address = \FriendsOfREDAXO\Simpleshop\CustomerAddress::get($params['list']->getValue('id'));
            $result  = $address ? $address->getName() : 'undefined';
        } else if ($table == \FriendsOfREDAXO\Simpleshop\Customer::TABLE) {
            $customer  = \FriendsOfREDAXO\Simpleshop\Customer::get($params['list']->getValue('id'));
            $addressId = $customer->getValue('invoice_address_id');
            $address   = $addressId ? \FriendsOfREDAXO\Simpleshop\CustomerAddress::get($addressId) : null;
            $result    = $address ? $address->getName() : 'undefined';
        }
        return $result;
    }

    public function getDefinitions($values = [])
    {
        return [
            'type'          => 'value',
            'name'          => 'list_name',
            'values'        => [
                'name'  => ['type' => 'name', 'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
            ],
            'description'   => 'Spezielles Feld für die Ausgabe von Namen in der Shop Übersicht',
            'dbtype'        => 'none',
            'list_hidden'   => false,
            'is_searchable' => false,
        ];
    }
}