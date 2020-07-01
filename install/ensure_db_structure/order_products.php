<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 22.06.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$prio  = 0;
$sql   = rex_sql::factory();
$table = \FriendsOfREDAXO\Simpleshop\OrderProduct::TABLE;

\Kreatif\Yform::ensureValueField($table, 'order_id', [], [
    'type_name'    => 'be_manager_relation',
    'list_hidden'  => 0,
    'search'       => 1,
    'label'        => 'Order',
    'db_type'      => 'int',
    'type'         => 2,
    'empty_option' => 0,
    'empty_value'  => 'Order-ID not set',
    'table'        => \FriendsOfREDAXO\Simpleshop\Order::TABLE,
    'field'        => 'id',
    'prio'         => $prio++,
]);

\Kreatif\Yform::ensureValueField($table, 'product_id', [], [
    'type_name'    => 'be_manager_relation',
    'list_hidden'  => 0,
    'search'       => 1,
    'label'        => 'Product',
    'db_type'      => 'int',
    'type'         => 2,
    'empty_option' => 0,
    'empty_value'  => 'Product-ID not set',
    'table'        => \FriendsOfREDAXO\Simpleshop\Product::TABLE,
    'field'        => 'name_1',
    'prio'         => $prio++,
]);

\Kreatif\Yform::ensureValueField($table, 'code', [], [
    'type_name'    => 'text',
    'list_hidden'  => 0,
    'search'       => 1,
    'label'        => 'Product-Code',
    'db_type'      => 'varchar(191)',
    'prio'         => $prio++,
]);

\Kreatif\Yform::ensureValueField($table, 'cart_quantity', [], [
    'type_name'    => 'integer',
    'db_type'      => 'int',
    'list_hidden'  => 0,
    'search'       => 0,
    'label'        => 'Quantity',
    'prio'         => $prio++,
]);

\Kreatif\Yform::ensureValueField($table, 'shipping_key', [], [
    'type_name'    => 'text',
    'db_type'      => 'varchar(191)',
    'list_hidden'  => 1,
    'search'       => 0,
    'label'        => 'Shipping-Key',
    'prio'         => $prio++,
]);

\Kreatif\Yform::ensureValueField($table, 'data', [], [
    'type_name'    => 'data_output',
    'db_type'      => 'text',
    'list_hidden'  => 1,
    'search'       => 0,
    'label'        => 'Product-Data',
    'prio'         => $prio++,
]);


\Kreatif\Yform::ensureValueField($table, 'createdate', [
    'type_name'   => 'datestamp',
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

\Kreatif\Yform::ensureValueField($table, 'updatedate', [
    'type_name'   => 'datestamp',
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

\Kreatif\Yform::ensureValueField($table, 'createuser', [
    'label' => 'translate:created_by',
    'prio'  => $prio++,
], [
    'type_name'   => 'be_user',
    'db_type'     => 'varchar(191)',
    'list_hidden' => 1,
    'search'      => 0,
    'only_empty'  => 1,
    'show_value'  => 1,
]);

\Kreatif\Yform::ensureValueField($table, 'updateuser', [
    'label' => 'translate:updated_by',
    'prio'  => $prio++,
], [
    'type_name'   => 'be_user',
    'db_type'     => 'varchar(191)',
    'list_hidden' => 1,
    'search'      => 0,
    'only_empty'  => 0,
    'show_value'  => 1,
]);

$sql->setTable('rex_yform_table');
$sql->setValue('name', 'Order-Products');
$sql->setValue('list_sortfield', 'id');
$sql->setValue('list_sortorder', 'DESC');
$sql->setValue('list_amount', 100);
$sql->setWhere(['table_name' => $table]);
$sql->update();

$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);

