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
class rex_yform_value_cst_shop_inputs extends rex_yform_value_text
{

    public function getDefinitions($values = [])
    {
        $config = [
            'type'            => 'value',
            'name'            => 'cst_shop_inputs',
            'dbtype'          => 'text',
            'multi_edit'      => false,
            'is_hiddeninlist' => false,
            'values'          => [
                'name'       => ['type' => 'name', 'label' => rex_i18n::msg("yform_values_defaults_name")],
                'label'      => ['type' => 'text', 'label' => rex_i18n::msg("yform_values_defaults_label")],
                'attributes' => ['type' => 'text', 'label' => rex_i18n::msg("yform_values_defaults_attributes"), 'notice' => rex_i18n::msg("yform_values_defaults_attributes_notice")],
                'notice'     => ['type' => 'text', 'label' => rex_i18n::msg("yform_values_defaults_notice")],
            ],
        ];

        switch ($values['name']) {
            case 'invoice_num':
                $config['is_hiddeninlist'] = !\FriendsOfREDAXO\Simpleshop\Utils::getSetting('use_invoicing', false);
                break;
        }
        return $config;
    }

    public function needsOutput()
    {
        $needOutput = parent::needsOutput();

        switch ($this->getName()) {
            case 'invoice_num':
                $needOutput = \FriendsOfREDAXO\Simpleshop\Utils::getSetting('use_invoicing', false);
                break;
        }
        return $needOutput;
    }
}