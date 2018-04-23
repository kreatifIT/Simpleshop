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


class FeatureValue extends Model
{
    const TABLE = 'rex_shop_feature_values';

    public static function getByVariantKey($vkey, $onlyNames = false)
    {
        $result     = [];
        $featureIds = explode(',', $vkey);

        foreach ($featureIds as $featureId) {
            if ($onlyNames) {
                $result[$featureId] = self::get($featureId)->getName();
            }
            else {
                $result[$featureId] = self::get($featureId);
            }
        }
        return $result;
    }

    public static function ext_yform_data_delete($params)
    {
        $result = $params->getSubject();

        if ($result !== false && $params->getParam('table')->getTableName() == self::TABLE) {
            $links    = [];
            $name     = 'name_'. \rex_clang::getCurrentId();
            $Addon    = \rex_addon::get('simpleshop');
            $obj_id   = $params->getParam('data_id');
            $products = Product::query()->resetSelect()->select('id')->select($name)->whereRaw("
                    features = {$obj_id}
                    OR features LIKE '{$obj_id},%'
                    OR features LIKE '%,{$obj_id},%'
                    OR features LIKE '%,{$obj_id}'
                ")->find();

            foreach ($products as $product) {
                $links[] = $product->getValue($name) . ' -
                    <a href="' . \rex_url::backendPage('yform/manager/data_edit', [
                        'data_id'    => $product->getValue('id'),
                        'table_name' => Product::TABLE,
                        'func'       => 'edit',
                    ]) . '" target="_blank">' . $Addon->i18n('action.edit_product') . '</a> |
                    <a href="' . $product->getUrl() . '" target="_blank">' . $Addon->i18n('action.show_in_frontend') . '</a>
                ';
            }
            if (count($links)) {
                $object  = FeatureValue::get($obj_id);
                $content = '<p>' . strtr($Addon->i18n('error.cannot_delete_value'), ['{{name}}' => $object->getValue($name)]) . '</p>';
                $content .= '<li>' . implode('</li><li>', $links) . '</li>';
                echo \rex_view::warning($content);
                $result = false;
            }
        }
        return $result;
    }
}