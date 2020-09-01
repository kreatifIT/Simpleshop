<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 25/08/2020
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class rex_yform_value_price_input extends rex_yform_value_number
{

    public function enterObject()
    {
        $dbPrice = null;

        if ($this->params['send']) {
            if ($this->params['main_id']) {
                if ($this->params['main_table'] == \FriendsOfREDAXO\Simpleshop\Product::TABLE) {
                    $productId = $this->params['main_id'];
                } else if ($this->params['main_table'] == \FriendsOfREDAXO\Simpleshop\Variant::TABLE) {
                    $variant   = \FriendsOfREDAXO\Simpleshop\Variant::get($this->params['main_id']);
                    $productId = $variant->getValue('product_id');
                }
                $product = \FriendsOfREDAXO\Simpleshop\Product::get($productId);
                $taxId   = $product->getValue('tax');
            } else if (rex_get('page', 'string') == 'simpleshop/variants' && $dataId = rex_get('data_id', 'int')) {
                $product = \FriendsOfREDAXO\Simpleshop\Product::get($dataId);
                $taxId   = $product->getValue('tax');
            } else {
                foreach ($this->params['values'] as $_valObject) {
                    if ($_valObject->getName() == 'tax') {
                        $taxId = $_valObject->getValue();
                        break;
                    }
                }
            }
            $tax = $taxId ? \FriendsOfREDAXO\Simpleshop\Tax::get($taxId) : null;

            if ($tax && $taxPerc = $tax->getValue('tax')) {
                $dbPrice = str_replace(',', '.', str_replace(',', '.', $this->getValue()) / ($taxPerc + 100) * 100);
            }
        } else if ($this->params['main_id']) {
            if ($this->params['main_table'] == \FriendsOfREDAXO\Simpleshop\Product::TABLE) {
                $product = \FriendsOfREDAXO\Simpleshop\Product::get($this->params['main_id']);
            } else if ($this->params['main_table'] == \FriendsOfREDAXO\Simpleshop\Variant::TABLE) {
                $variant = \FriendsOfREDAXO\Simpleshop\Variant::get($this->params['main_id']);
                $product = \FriendsOfREDAXO\Simpleshop\Product::get($variant->getValue('product_id'));
                $product->setValue($this->getName(), $variant->getValue($this->getName()));
            }
            $this->setValue($product->getPrice(true));
        }

        $this->setValue(number_format((float)str_replace(',', '.', $this->getValue()), 2, '.', ''));

        parent::enterObject();

        if ($dbPrice !== null) {
            $this->params['value_pool']['email'][$this->getName()] = $dbPrice;
            if ($this->saveInDb()) {
                $this->params['value_pool']['sql'][$this->getName()] = $dbPrice;
            }
        } 
    }

    public static function getListValue($params)
    {
        $item  = \FriendsOfREDAXO\Simpleshop\Product::get($params['list']->getValue('id'));
        $field = \FriendsOfREDAXO\Simpleshop\Product::getYformFieldByName($params['field']);

        return format_price($item->getPrice(true)) . " {$field->getElement('unit')}";
    }

    public function getDefinitions($values = [])
    {
        $params         = parent::getDefinitions($values);
        $params['name'] = 'price_input';

        return $params;
    }
}