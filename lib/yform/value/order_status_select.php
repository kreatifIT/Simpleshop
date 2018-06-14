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
class rex_yform_value_order_status_select extends rex_yform_value_select
{
    public function enterObject()
    {
        $options  = [];
        $_options = $this->getArrayFromString($this->getElement('options'));

        if ($this->getParam('main_id')) {
            if (isset($_options['CN'])) {
                $Order = \FriendsOfREDAXO\Simpleshop\Order::get($this->getParam('main_id'));

                if ($Order->getValue('ref_order_id')) {
                    $options = ['CN' => $_options['CN']];
                }
                else if (count(\FriendsOfREDAXO\Simpleshop\Order::query()->where('ref_order_id', $Order->getId())->find())) {
                    $options = ['CA' => $_options['CA']];
                }
            }
            if (count($options) == 0) {
                unset($_options['CN']);
                $options = $_options;
            }
            $this->setElement('options', $options);
        }

        return parent::enterObject();
    }

    public function getDefinitions()
    {
        return [
            'type'            => 'value',
            'name'            => 'order_status_select',
            'values'          => [
                'name'       => ['type' => 'name', 'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label'      => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
                'options'    => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_select_options')],
                'default'    => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_select_default')],
                'attributes' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_attributes'), 'notice' => rex_i18n::msg('yform_values_defaults_attributes_notice')],
                'notice'     => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_notice')],
            ],
            'description'     => 'Bestellungs-Statuse',
            'dbtype'          => 'text',
            'famous'          => false,
            'is_hiddeninlist' => true,
            'is_searchable'   => true,
        ];
    }
}