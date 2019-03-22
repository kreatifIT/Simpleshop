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
class rex_yform_value_order_products extends rex_yform_value_abstract
{

    public function enterObject()
    {
        if (rex::isBackend() && $this->getParam('main_id')) {
            $Order = \FriendsOfREDAXO\Simpleshop\Order::get($this->getParam('main_id'));

            $this->params['form_output'][$this->getId()] = $this->parse('value.order_products.tpl.php', [
                'Order' => $Order,
            ]);
        }
    }

    public function getDefinitions($values = [])
    {
        return [
            'is_hiddeninlist' => true,
            'is_searchable'   => false,
            'dbtype'          => 'none',
            'type'            => 'value',
            'name'            => 'order_products',
            'values'          => [
                'name'    => ['type' => 'name', 'label' => rex_i18n::msg("yform_values_defaults_name")],
                'label'   => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
                'notice'  => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_notice')],
            ],
        ];
    }
}