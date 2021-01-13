<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 16.06.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ADDRESS table fields
$table = \FriendsOfREDAXO\Simpleshop\CustomerAddress::TABLE;
$field = \FriendsOfREDAXO\Simpleshop\CustomerAddress::getYformFieldByName('customer_id');
$prio = $field->getElement('prio');

\Kreatif\Yform::ensureValueField($table, 'ombis_id', [], [
    'type_name'   => 'integer',
    'list_hidden' => 1,
    'search'      => 1,
    'label'       => 'Ombis-ID',
    'db_type'     => 'int',
    'attributes'  => '{"readonly":"readonly"}',
    'prio'        => $prio++
]);

\Kreatif\Yform::ensureValueField($table, 'ombis_uid', [], [
    'type_name'   => 'text',
    'list_hidden' => 1,
    'search'      => 1,
    'label'       => 'Ombis-UUID',
    'db_type'     => 'varchar(191)',
    'attributes'  => '{"readonly":"readonly"}',
    'prio'        => $prio++
]);

$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// CUSTOMER table fields
$table = \FriendsOfREDAXO\Simpleshop\Customer::TABLE;
$field = \FriendsOfREDAXO\Simpleshop\Customer::getYformFieldByName('invoice_address_id');


\Kreatif\Yform::ensureValueField($table, 'ombis_id', [], [
    'type_name'   => 'integer',
    'list_hidden' => 1,
    'search'      => 1,
    'label'       => 'Ombis-ID',
    'db_type'     => 'int',
    'attributes'  => '{"readonly":"readonly"}',
    'prio'        => $field->getElement('prio') + 1,
]);


$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// COUNTRY table fields
$table = \FriendsOfREDAXO\Simpleshop\Country::TABLE;
$field = \FriendsOfREDAXO\Simpleshop\Country::getYformFieldByName('prio');


\Kreatif\Yform::ensureValueField($table, 'ombis_id', [], [
    'type_name'   => 'integer',
    'list_hidden' => 1,
    'search'      => 1,
    'label'       => 'Ombis-ID',
    'db_type'     => 'int',
    'attributes'  => '{"readonly":"readonly"}',
    'prio'        => $field->getElement('prio') - 1,
]);


$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ORDER table fields
$table = \FriendsOfREDAXO\Simpleshop\Order::TABLE;
$field = \FriendsOfREDAXO\Simpleshop\Order::getYformFieldByName('invoice_num');


\Kreatif\Yform::ensureValueField($table, 'ombis_id', [], [
    'type_name'   => 'integer',
    'list_hidden' => 1,
    'search'      => 1,
    'label'       => 'Ombis-ID',
    'db_type'     => 'int',
    'attributes'  => '{"readonly":"readonly"}',
    'prio'        => $field->getElement('prio') + 1,
]);


$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);