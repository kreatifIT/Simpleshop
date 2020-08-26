<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 26.08.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$prio  = 0;
$sql   = rex_sql::factory();
$langs = array_values(rex_clang::getAll());
$table = \FriendsOfREDAXO\Simpleshop\Variant::TABLE;


\Kreatif\Yform::ensureValueField($table, 'product_id', 'be_manager_relation', [
    'label' => 'Product',
    'prio'  => $prio++,
], [
    'db_type'      => 'int',
    'table'        => 'rex_shop_product',
    'field'        => 'name_1',
    'empty_option' => 0,
    'type'         => 0,
    'list_hidden'  => 1,
    'search'       => 0,
    'multiple'     => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'type', 'choice', [
    'label'   => 'translate:label.type',
    'prio'    => $prio++,
    'default' => 'product',
], [
    'choices'     => json_encode([
        'translate:label.active'   => 'A',
        'translate:label.inactive' => 'NE',
    ]),
    'db_type'     => 'text',
    'fields'      => 'name_1',
    'list_hidden' => 1,
    'search'      => 0,
    'no_db'       => 0,
    'multiple'    => 0,
    'expanded'    => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'variant_key', 'text', [
    'label' => 'Variant-Key',
    'prio'  => $prio++,
], [
    'list_hidden' => 1,
    'search'      => 0,
    'db_type'     => 'varchar(191)',
]);

\Kreatif\Yform::ensureValueField($table, 'code', 'text', [
    'label' => '###label.product_code###',
    'prio'  => $prio++,
], [
    'list_hidden' => 1,
    'search'      => 0,
    'db_type'     => 'varchar(191)',
]);

if (\FriendsOfREDAXO\Simpleshop\Variant::getYformFieldByName('amount')) {
    \Kreatif\Yform::ensureValueField($table, 'amount', 'integer', [
        'label' => 'translate:label.amount',
        'prio'  => $prio++,
    ], [
        'list_hidden' => 1,
        'search'      => 0,
        'db_type'     => 'int',
    ]);
}

if (\FriendsOfREDAXO\Simpleshop\Variant::getYformFieldByName('price')) {
    \Kreatif\Yform::ensureValueField($table, 'price', 'price_input', [
        'label' => 'translate:label.gross_single_price',
        'prio'  => $prio++,
    ], [
        'list_hidden' => 1,
        'search'      => 0,
        'db_type'     => '',
        'no_db'       => 0,
        'scale'       => 6,
        'precision'   => 13,
        'unit'        => '',
    ]);
}

if (\FriendsOfREDAXO\Simpleshop\Variant::getYformFieldByName('reduced_price')) {
    \Kreatif\Yform::ensureValueField($table, 'reduced_price', 'price_input', [
        'label' => 'translate:label.reduced_gross_single_price',
        'prio'  => $prio++,
    ], [
        'list_hidden' => 1,
        'search'      => 0,
        'db_type'     => '',
        'no_db'       => 0,
        'scale'       => 6,
        'precision'   => 13,
        'unit'        => '',
    ]);
}

\Kreatif\Yform::ensureValueField($table, 'prio', 'integer', [
    'label' => 'translate:label.priority',
    'prio'  => $prio++,
], [
    'list_hidden' => 1,
    'search'      => 0,
    'fields'      => 'name_1',
    'attributes'  => '{"class":"prio","type":"hidden"}',
]);

\Kreatif\Yform::ensureValueField($table, 'createdate', 'datestamp', [
    'list_hidden' => 0,
    'search'      => 0,
    'show_value'  => 1,
    'label'       => 'translate:label.created_at',
    'prio'        => $prio++,
], [
    'db_type'    => 'datetime',
    'format'     => 'Y-m-d H:i:s',
    'only_empty' => 1,
    'no_db'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'updatedate', 'datestamp', [
    'list_hidden' => 1,
    'search'      => 0,
    'show_value'  => 1,
    'label'       => 'translate:label.updated_at',
    'prio'        => $prio++,
], [
    'db_type'    => 'datetime',
    'format'     => 'Y-m-d H:i:s',
    'only_empty' => 0,
    'no_db'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'createuser', 'be_user', [
    'label' => 'translate:created_by',
    'prio'  => $prio++,
], [
    'db_type'     => 'varchar(191)',
    'list_hidden' => 1,
    'search'      => 0,
    'only_empty'  => 1,
    'show_value'  => 1,
]);

\Kreatif\Yform::ensureValueField($table, 'updateuser', 'be_user', [
    'label' => 'translate:updated_by',
    'prio'  => $prio++,
], [
    'db_type'     => 'varchar(191)',
    'list_hidden' => 1,
    'search'      => 0,
    'only_empty'  => 0,
    'show_value'  => 1,
]);

$sql->setTable('rex_yform_table');
$sql->setValue('prio', 17);
$sql->setWhere(['table_name' => $table]);
$sql->update();

$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);