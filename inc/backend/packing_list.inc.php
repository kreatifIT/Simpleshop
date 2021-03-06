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
    $Settings    = \rex::getConfig('simpleshop.Settings');

    if (from_array($Settings, 'packing_list_printing') == 1 && $list_params['table_name'] == Order::TABLE) {
        $list->addColumn('packing_list', $this->i18n('action.print_packing_list'), count($list->getColumnNames()));
        $list->setColumnLabel('packing_list', '');
        $list->setColumnParams('packing_list', [
            'page'       => 'yform/manager/data_edit',
            'table_name' => Order::TABLE,
            'data_id'    => '###id###',
            'func'       => 'edit',
            'ss-action'  => 'generate_packing_list',
        ]);
    }
    return $list;
});