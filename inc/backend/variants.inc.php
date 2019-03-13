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

\rex_extension::register('YFORM_DATA_LIST', function ($params) {
    $list        = $params->getSubject();
    $list_params = $list->getParams();

    if (FragmentConfig::getValue('has_variants') && $list_params['table_name'] == Product::TABLE) {
        $url = \rex_url::backendPage('simpleshop/variants', [
            'table_name' => Variant::TABLE,
            'data_id'    => '-ID-',
            'func'       => 'edit',
        ]);
        $list->addColumn('variants', $this->i18n('action.manage_variants'), count($list->getColumnNames()) - 2);
        $list->setColumnLabel('variants', $this->i18n('label.variants'));
        $list->setColumnFormat('variants', 'custom', function ($params) {
            $Product = Product::get($params['list']->getValue('id'));

            if ($Product->getValue('type') != 'package') {
                $url = strtr($params['params']['url'], ['-ID-' => $params['list']->getValue('id')]);
                return "<a href='{$url}'>{$params['subject']}</a>";
            }
        }, ['url' => $url]);
    }
    return $list;
});