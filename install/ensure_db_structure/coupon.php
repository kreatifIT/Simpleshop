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

$sql   = rex_sql::factory();
$table = \FriendsOfREDAXO\Simpleshop\Coupon::TABLE;

\Kreatif\Yform::ensureValueField($table, 'coupon_functions', [], [
    'type_name'   => 'coupon_functions',
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => '',
    'db_type'     => 'none',
    'prio'        => 1,
]);

\Kreatif\Yform::ensureValueField($table, 'is_multi_use', [
    'type_name'   => 'choice',
    'list_hidden' => 0,
    'search'      => 0,
    'label'       => 'translate:coupon.use_settings',
], [
    'db_type'  => 'int',
    'default'  => 0,
    'no_db'    => 0,
    'multiple' => 0,
    'expanded' => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'start_time', [
    'type_name'    => 'date',
    'list_hidden'  => 0,
    'search'       => 0,
    'current_date' => 1,
    'label'        => 'translate:label.start_date',
    'widget'       => 'input:text',
    'format'       => 'DD.MM.YYYY',
    'attributes'   => '{"data-yform-tools-datepicker":"YYYY-MM-DD"}',
], [
    'db_type' => 'date',
    'default' => 0,
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'discount_value', [
    'type_name'   => 'number',
    'list_hidden' => 1,
    'search'      => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
    'label'       => 'translate:coupon.fixed_discount',
], [
    'db_type' => '',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'discount_percent', [
    'type_name'   => 'number',
    'list_hidden' => 1,
    'search'      => 0,
    'scale'       => 1,
    'precision'   => 4,
    'unit'        => '%',
    'label'       => 'translate:coupon.percent_discount',
], [
    'db_type' => '',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'prefix', [
    'type_name'   => 'text',
    'list_hidden' => 1,
    'search'      => 1,
    'label'       => 'translate:label.prefix',
    'attributes'  => '{"placeholder":"AA"}',
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'code', [
    'type_name'   => 'coupon_code',
    'list_hidden' => 0,
    'search'      => 1,
    'label'       => 'Code',
], [
    'db_type'    => 'varchar(191)',
    'attributes' => '{"readonly":"readonly"}',
    'no_db'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'orders', [
    'type_name'   => 'data_output',
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:coupon.use_info',
], [
    'db_type' => 'text',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'createdate', [
    'type_name'   => 'datestamp',
    'list_hidden' => 0,
    'search'      => 0,
    'show_value'  => 1,
    'label'       => 'translate:label.created_at',
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
], [
    'db_type'    => 'datetime',
    'format'     => 'Y-m-d H:i:s',
    'only_empty' => 0,
    'no_db'      => 0,
]);

$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);
