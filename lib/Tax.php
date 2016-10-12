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

class Tax extends \rex_yform_manager_dataset
{
    const TABLE = 'rex_shop_tax';

    public static function ext_yform_data_delete($params)
    {
        $result = $params->getSubject();

        if ($result !== FALSE && $params->getParam('table')->getTableName() == self::TABLE)
        {
            $Addon    = \rex_addon::get('simpleshop');
            $links    = [];
            $obj_id   = $params->getParam('data_id');
            $products = Product::query()
                ->resetSelect()
                ->select('id')
                ->select('tax')
                ->where('tax', $obj_id)
                ->find();

            foreach ($products as $product)
            {
                $links[] = $product->getValue('tax') .' -
                    <a href="'. \rex_url::backendPage('yform/manager/data_edit', ['data_id' => $product->getValue('id'), 'table_name' => Product::TABLE, 'func' => 'edit',]) .'" target="_blank">'. $Addon->i18n('action.edit_product') .'</a> |
                    <a href="'. $product->getUrl() .'" target="_blank">'. $Addon->i18n('action.show_in_frontend') .'</a>
                ';
            }
            if (count($links))
            {
                $object   = Tax::get($obj_id);
                $content  = '<p>'. strtr($Addon->i18n('error.cannot_delete_tax'), ['{{name}}' => $object->getValue('tax')]) .'</p>';
                $content .= '<li>'. implode('</li><li>', $links) .'</li>';
                echo \rex_view::warning($content);
                $result = FALSE;
            }
        }
        return $result;
    }
}