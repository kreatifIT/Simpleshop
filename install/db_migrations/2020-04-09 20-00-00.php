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
        'table' => 'rex_shop_product',
        'type'  => 'value',
        'name'  => 'product_functions',
    ]));
    if (!$item) {
        $sql->setTable('rex_yform_field');
        $sql->setValue('table_name', 'rex_shop_product');
        $sql->setValue('prio', 1);
        $sql->setValue('type_id', 'value');
        $sql->setValue('type_name', 'product_functions');
        $sql->setValue('db_type', 'none');
        $sql->setValue('list_hidden', 1);
        $sql->setValue('search', 0);
        $sql->setValue('name', 'product_functions');
        $sql->setValue('table', 'rex_shop_product');
        $sql->setValue('empty_option', 0);
        $sql->insert();
    }

    $item = current($sql->getArray('SELECT id FROM rex_yform_field WHERE table_name = :table AND type_id = :type AND type_name = :type_name AND name = :name', [
        'table'     => 'rex_shop_product',
        'type'      => 'validate',
        'type_name' => 'unique',
        'name'      => 'code',
    ]));
    if (!$item) {
        $sql->setTable('rex_yform_field');
        $sql->setValue('table_name', 'rex_shop_product');
        $sql->setValue('prio', 1);
        $sql->setValue('type_id', 'validate');
        $sql->setValue('type_name', 'unique');
        $sql->setValue('list_hidden', 1);
        $sql->setValue('search', 0);
        $sql->setValue('name', 'code');
        $sql->setValue('message', 'Der angegeben Produktcode wird bereits für ein anderes Produkt verwendet');
        $sql->insert();
    }
}
finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}