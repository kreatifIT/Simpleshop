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

\rex_extension::register('YFORM_DATA_LIST', function ($params)
{
    $list         = $params->getSubject();
//    $list_params  = $list->getParams();
//    $variant_type = \rex::getProperty('simpleshop.product_variants');
//
//    if ($variant_type && $list_params['table_name'] == Product::TABLE)
//    {
//        $list->addColumn('variants', $this->i18n('action.manage_variants'), count($list->getColumnNames()) - 2);
//        $list->setColumnLabel('variants', $this->i18n('label.variants'));
//        $list->setColumnParams('variants', [
//            'page'       => "simpleshop/{$variant_type}",
//            'func'       => 'edit',
//            'data_id'    => '###id###',
//            'table_name' => Variant::TABLE,
//        ]);
//    }
    return $list;
});