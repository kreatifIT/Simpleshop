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


class Variant extends Model
{
    const TABLE = "rex_shop_product_has_feature";

    public static function getByVariantKey($key)
    {
        list ($product_id, $variant_key) = explode('|', $key);
        return self::query()->where('product_id', $product_id)->where('variant_key', $variant_key)->findOne();
    }

    public function applyProductData()
    {
        $_product = Product::get($this->getValue('product_id'));
        $product  = clone $_product;
        $product->applyVariantData($this->getData());
        $product_data = $product->getData();

        foreach ($product_data as $key => $value) {
            $this->setValue($key, $value);
        }
    }

    public function isOnline($lang_id = null)
    {
        return true;
    }

    public static function be_getYFields()
    {
        $labels  = [];
        $fields  = [];
        $columns = \rex_yform_manager_table::get(self::TABLE)->getFields();
        $params  = ['this' => \rex_yform::factory()];

        foreach ($columns as $column) {
            $type = $column->getTypeName();
            $name = $column->getName();

            if ($name == 'variant_key' || in_array($type, ['be_manager_relation', 'datestamp'])) {
                continue;
            }
            $notice      = '';
            $values      = [$type];
            $class       = 'rex_yform_value_' . trim($type);
            $field       = new $class();
            $definitions = $field->getDefinitions();

            foreach ($definitions['values'] as $key => $_) {
                $values[] = $column->getElement($key);
            }

            if (count($values) > 4) {
                $i          = count($values) - 1;
                $notice     = $values[$i];
                $values[$i] = '';
            }
            $field->loadParams($params, $values);
            $fields[] = $field;

            if ($type != 'hidden_input') {
                $labels[] = [
                    'label'  => $field->getLabel(),
                    'notice' => $notice,
                ];
            }
        }
        return [$labels, $fields];
    }
}