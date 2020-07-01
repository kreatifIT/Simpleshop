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
        $order = $this->params['main_id'] ? \FriendsOfREDAXO\Simpleshop\Order::get($this->params['main_id']) : null;

        if ($this->getValue() == '' && $order) {
            $address = $order->getShippingAddress();
            if ($address) {
                $this->setValue($address->getId());
            }
        }

        if ($this->params['send'] == 1 && $this->getValue() != '') {
            if ($this->params['main_table'] == \FriendsOfREDAXO\Simpleshop\Order::TABLE) {
                $currentAddressId = $order ? $order->getValue($this->getName(null, true)) : 0;

                if ($this->getValue() != $currentAddressId) {
                    // customer daten nur aktualisieren wenn sich kundenadresse geändert hat (manuell im Backend geändert worden)!
                    $address    = \FriendsOfREDAXO\Simpleshop\CustomerAddress::get($this->getValue());
                    $customerId = $address->getValue('customer_id');
                    $customer   = $customerId ? \FriendsOfREDAXO\Simpleshop\Customer::get($customerId) : null;

                    $this->params['value_pool']['sql']['customer_data']    = $customer;
                    $this->params['value_pool']['sql']['customer_id']      = $customerId;
                    $this->params['value_pool']['sql']['shipping_address'] = $address;
                } else if ($order) {
                    $this->params['value_pool']['sql']['customer_id']      = $order->getValue('customer_id');
                    $this->params['value_pool']['sql']['customer_data']    = $order->getRawValue('customer_data');
                    $this->params['value_pool']['sql']['shipping_address'] = $order->getRawValue('shipping_address');
                }
            } else if ((int)$this->getElement('empty_option') == 0) {
                $this->params['warning'][$this->getId()]          = $this->params['error_class'];
                $this->params['warning_messages'][$this->getId()] = $this->getElement('empty_value');
            }
        }

        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse('value.customer_address.tpl.php');
        }

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
        $this->params['value_pool']['sql'][$this->getName()]   = $this->getValue();
    }

    public static function getListValue($params)
    {
        $orderId = $params['list']->getValue('id');
        $order   = \FriendsOfREDAXO\Simpleshop\Order::get($orderId);

        $invoiceAddr  = $order->getInvoiceAddress();
        $shippingAddr = $order->getShippingAddress();

        $value = implode(' | ', array_unique(array_filter([
            $invoiceAddr->getName(null, true),
            $shippingAddr->getName(null, true),
        ])));
        return $value;
    }

    public function getDefinitions($values = [])
    {
        return [
            'db_null'     => true,
            'dbtype'      => 'int',
            'type'        => 'value',
            'name'        => 'customer_address',
            'description' => 'Auswahl Kundenadresse',
            'values'      => [
                'name'         => ['type' => 'name', 'label' => rex_i18n::msg("yform_values_defaults_name")],
                'label'        => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
                'notice'       => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_notice')],
                'empty_option' => ['type' => 'boolean', 'label' => rex_i18n::msg('yform_values_be_manager_relation_empty_option')],
                'empty_value'  => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_be_manager_relation_empty_value')],
            ],
        ];
    }
}