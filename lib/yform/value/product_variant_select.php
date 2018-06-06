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
class rex_yform_value_product_variant_select extends rex_yform_value_select
{
    public function enterObject()
    {
        $multiple = $this->getElement('multiple') == 1;
        $lvalues  = [];

        $values = $this->getValue();
        if (!is_array($values)) {
            $values = explode('+', $values);
        }
        $values = array_filter($values);

        $real_values = [];
        foreach ($values as $value) {
            $Product = \FriendsOfREDAXO\Simpleshop\Product::getProductVariant($value);

            if ($Product) {
                $real_values[] = $value;

                if ($Product->valueIsset('variant_key')) {
                    $_flabel    = [];
                    $featureIds = explode(',', $Product->getValue('variant_key'));

                    foreach ($featureIds as $featureId) {
                        $_flabel[] = \FriendsOfREDAXO\Simpleshop\FeatureValue::get($featureId)->getName();
                    }

                    $lvalues[$value] = "[{$Product->getValue('code')}]  {$Product->getName()}  |  " . implode(' + ', $_flabel);
                }
                else {
                    $lvalues[$value] = "[{$Product->getValue('code')}] {$Product->getName()}";
                }

                if (!$multiple) {
                    break;
                }
            }
        }

        $this->setValue($real_values);

        // ---------- rex_yform_set
        if (isset($this->params['rex_yform_set'][$this->getName()]) && !is_array($this->params['rex_yform_set'][$this->getName()])) {
            $value  = $this->params['rex_yform_set'][$this->getName()];
            $values = [];
            if (array_key_exists($value, $lvalues)) {
                $values[] = (string) $value;
            }
            $this->setValue($values);
            $this->setElement('disabled', true);
        }

        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse('value.product_variant_select.tpl.php', compact('lvalues', 'multiple', 'size'));
        }

        $this->setValue(implode('+', (array) $this->getValue()));

        $this->params['value_pool']['email'][$this->getName()]           = $this->getValue();
        $this->params['value_pool']['email'][$this->getName() . '_NAME'] = count($lvalues) ? implode(', ', $lvalues) : (isset($lvalues[$this->getValue()]) ? $lvalues[$this->getValue()] : null);
        $this->params['value_pool']['sql'][$this->getName()]             = $this->getValue();
    }

    public function getDefinitions($values = [])
    {
        return [
            'type'        => 'value',
            'name'        => 'product_variant_select',
            'values'      => [
                'name'       => ['type' => 'name', 'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label'      => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
                'multiple'   => ['type' => 'boolean', 'label' => rex_i18n::msg('yform_values_select_multiple')],
                'attributes' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_attributes'), 'notice' => rex_i18n::msg('yform_values_defaults_attributes_notice')],
                'notice'     => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_notice')],
            ],
            'description' => 'Produkt-Varianten',
            'dbtype'      => 'text',
            'famous'      => false,
        ];
    }
}