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

class Feature extends \rex_yform_manager_dataset
{
    const TABLE = 'rex_shop_feature';
    private $values = NULL;

    public static function getFeatureByKey($key)
    {
        return self::query()->where('key', $key)->findOne();
    }

    public function getValues($ignore_offline = TRUE)
    {
        if ($this->values === NULL)
        {
            $this->values = FeatureValue::query()
                ->where('feature_id', $this->getValue('id'))
                ->where('status', (int) $ignore_offline)
                ->find();
        }
        return $this->values;
    }

    public static function ext_yform_data_delete($params)
    {
        $result = $params->getSubject();

        if ($result !== FALSE && $params->getParam('table')->getTableName() == self::TABLE)
        {
            $Addon  = \rex_addon::get('simpleshop');
            $links  = [];
            $name   = sprogfield('name');
            $obj_id = $params->getParam('data_id');
            $values = FeatureValue::query()
                ->resetSelect()
                ->select('id')
                ->select($name)
                ->where('feature_id', $obj_id)
                ->find();

            foreach ($values as $value)
            {
                $links[] = $value->getValue($name) . ' -
                    <a href="' . \rex_url::backendPage('yform/manager/data_edit', ['data_id' => $value->getValue('id'), 'table_name' => FeatureValue::TABLE, 'func' => 'edit',]) . '" target="_blank">' . $Addon->i18n('action.edit_value') . '</a>
                ';
            }
            if (count($links))
            {
                $object  = Feature::get($obj_id);
                $content = '<p>' . strtr($Addon->i18n('error.cannot_delete_feature'), ['{{name}}' => $object->getValue($name)]) . '</p>';
                $content .= '<li>' . implode('</li><li>', $links) . '</li>';
                echo \rex_view::warning($content);
                $result = FALSE;
            }
        }
        return $result;
    }
}