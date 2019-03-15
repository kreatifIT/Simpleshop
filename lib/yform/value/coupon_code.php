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
        if ($this->getValue() == '' && $this->params['send'])
        {
            $code = $this->getRandomCode();

            $this->setValue($code);
            $this->params['value_pool']['email'][$this->getName()] = $code;
            $this->params['value_pool']['sql'][$this->getName()]   = $code;
        }
        if ($this->params['send'])
        {
            $clones = rex_post('clone', 'int');
            $_POST['clone'] = 0;

            if ($clones)
            {
                // cloning codes
                for ($i = 0; $i < $clones; $i++)
                {
                    $clone = \FriendsOfREDAXO\Simpleshop\Coupon::create();
                    foreach ($this->params['value_pool']['sql'] as $name => $value)
                    {
                        $clone->setValue($name, $value);
                    }
                    $clone->setValue('given_away', 0);
                    $clone->setValue('orders', NULL);
                    $clone->setValue('code', $this->getRandomCode());
                    $clone->setValue('createdate', date('Y-m-d H:i:s'));
                    $clone->save();
                }
            }
        }
        else
        {
            $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
        }
    }

    public static function getRandomCode()
    {
        do
        {
            $code = strtoupper(random_string(4)) .'-'. strtoupper(random_string(4));
        }
        while (!empty (\FriendsOfREDAXO\Simpleshop\Coupon::query()->where('code', $code)->findOne()));

        return $code;
    }

    function getDescription()
    {
        return 'coupon_code|name|[no_db]';
    }

    function getDefinitions($values = [])
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
            'multi_edit' => FALSE,
        ];
    }

    static function getListValue($params)
    {
        $prefix = $params['list']->getValue('prefix');

        if ($prefix != '')
        {
            $prefix .= '-';
        }
        return $prefix . $params['subject'];
    }
}