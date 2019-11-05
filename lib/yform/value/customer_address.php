<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 18.03.19
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class rex_yform_value_customer_address extends rex_yform_value_abstract
{
    protected static $customerSaved = false;

    public function enterObject()
    {
        if (rex::isBackend()) {
            if ($this->params['send'] == 1) {
                $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
                $this->params['value_pool']['sql'][$this->getName()]   = $this->getValue();

                if ($this->getValue() != '') {
                    $address = \FriendsOfREDAXO\Simpleshop\CustomerAddress::get($this->getValue());
                    $this->params['value_pool']['sql']['customer_id'] = $address->getValue('customer_id');
                }
            }

            $this->params['form_output'][$this->getId()] = $this->parse('value.customer_address.tpl.php');
        }
    }

    public static function getListValue($params)
    {
        $value   = $params['subject'];
        $address = $value ? \FriendsOfREDAXO\Simpleshop\CustomerAddress::get($value) : null;

        if ($address) {
            $customer = \FriendsOfREDAXO\Simpleshop\Customer::get($address->getValue('customer_id'));
            $value    = $customer ? $customer->getName(null, true) : $address->getName();
        }
        return $value;
    }

    public function getDefinitions($values = [])
    {
        return [
            'dbtype'      => 'int',
            'type'        => 'value',
            'name'        => 'customer_address',
            'description' => 'Auswahl Kundenadresse',
            'values'      => [
                'name'   => ['type' => 'name', 'label' => rex_i18n::msg("yform_values_defaults_name")],
                'label'  => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
                'notice' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_notice')],
            ],
        ];
    }
}