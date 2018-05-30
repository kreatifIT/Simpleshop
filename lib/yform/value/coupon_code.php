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
class rex_yform_value_coupon_code extends rex_yform_value_text
{
    function postFormAction()
    {
        if ($this->getValue() == '' && $this->params['send']) {
            $code = $this->getRandomCode();

            $this->setValue($code);
            $this->params['value_pool']['email'][$this->getName()] = $code;
            $this->params['value_pool']['sql'][$this->getName()]   = $code;
        }
    }

    public static function getRandomCode()
    {
        do {
            $code = strtoupper(random_string(4)) . '-' . strtoupper(random_string(4));
        } while (!empty (\FriendsOfREDAXO\Simpleshop\Coupon::query()
            ->where('code', $code)
            ->findOne()));

        return $code;
    }

    function getDescription()
    {
        return 'coupon_code|name|[no_db]';
    }

    function getDefinitions()
    {
        return [
            'type'       => 'value',
            'name'       => 'coupon_code',
            'values'     => [
                'name'       => ['type' => 'name', 'label' => rex_i18n::msg("yform_values_defaults_name")],
                'label'      => ['type' => 'text', 'label' => rex_i18n::msg("yform_values_defaults_label")],
                'attributes' => ['type' => 'text', 'label' => rex_i18n::msg("yform_values_defaults_attributes"), 'notice' => rex_i18n::msg("yform_values_defaults_attributes_notice")],
                'notice'     => ['type' => 'text', 'label' => rex_i18n::msg("yform_values_defaults_notice")],
            ],
            'dbtype'     => 'text',
            'multi_edit' => false,
        ];
    }

    static function getListValue($params)
    {
        $prefix = $params['list']->getValue('prefix');

        if ($prefix != '') {
            $prefix .= '-';
        }
        return $prefix . $params['subject'];
    }
}