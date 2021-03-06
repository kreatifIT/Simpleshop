<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 24.02.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


$sql = rex_sql::factory();
$sql->setQuery('SET FOREIGN_KEY_CHECKS = 0');

try {

    $item = current($sql->getArray('SELECT id FROM rex_yform_field WHERE table_name = :table AND type_id = :type AND name = :name', [
        'table' => 'rex_shop_order',
        'type'  => 'value',
        'name'  => 'remarks',
    ]));
    if (!$item) {
        $sql->setTable('rex_yform_field');
        $sql->setValue('table_name', 'rex_shop_order');
        $sql->setValue('prio', 5);
        $sql->setValue('type_id', 'value');
        $sql->setValue('type_name', 'textarea');
        $sql->setValue('db_type', 'text');
        $sql->setValue('list_hidden', 1);
        $sql->setValue('search', 0);
        $sql->setValue('name', 'remarks');
        $sql->setValue('label', '###label.remarks_infos###');
        $sql->insert();
    }

    rex_sql_table::get('rex_shop_order')
        ->ensureColumn(new rex_sql_column('remarks', 'text'), 'invoice_num')
        ->alter();


    rex_sql_table::get('rex_shop_product_has_feature')
        ->ensureColumn(new rex_sql_column('price', 'decimal(11,4)', true))
        ->ensureColumn(new rex_sql_column('reduced_price', 'decimal(11,4)', true))
        ->ensure();

    $sql->setTable('rex_yform_field');
    $sql->setValue('table_name', 'rex_shop_product_has_feature');
    $sql->setValue('name', 'price');
    $sql->setValue('type_name', 'number');
    $sql->setValue('db_type', '');
    $sql->setValue('scale', 4);
    $sql->setValue('precision', 11);
    $sql->setWhere([
        'table_name' =>'rex_shop_product_has_feature',
        'name' =>'price',
    ]);
    $sql->update();

    $sql->setTable('rex_yform_field');
    $sql->setValue('table_name', 'rex_shop_product_has_feature');
    $sql->setValue('name', 'reduced_price');
    $sql->setValue('type_name', 'number');
    $sql->setValue('db_type', '');
    $sql->setValue('scale', 4);
    $sql->setValue('precision', 11);
    $sql->setWhere([
        'table_name' =>'rex_shop_product_has_feature',
        'name' =>'reduced_price',
    ]);
    $sql->update();
}
finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}