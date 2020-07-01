<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 29.06.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$prio  = 0;
$sql   = rex_sql::factory();
$table = \FriendsOfREDAXO\Simpleshop\DiscountGroup::TABLE;

\Kreatif\Yform::ensureValueField($table, 'tab_start', [], [
    'type_name'   => 'tab_start',
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => '',
    'db_type'     => 'none',
    'prio'        => $prio++,
]);

\Kreatif\Yform::ensureValueField($table, 'name_1', [
    'label'       => 'translate:label.designation',
    'list_hidden' => 0,
    'search'      => 1,
], [
    'type_name' => 'text',
    'db_type'   => 'varchar(191)',
    'prio'      => $prio++,
]);

$prio += 30;

\Kreatif\Yform::ensureValueField($table, 'tab_end', [], [
    'type_name'   => 'tab_end',
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => '',
    'db_type'     => 'none',
    'prio'        => $prio++,
]);

\Kreatif\Yform::ensureValueField($table, 'ctype', [
    'type_name'   => 'choice',
    'list_hidden' => 0,
    'search'      => 0,
    'label'       => '###label.customer_type###',
    'prio'        => $prio++,
    'choices'     => json_encode([
        '###label.all###'            => 'all',
        '###label.private_person###' => 'person',
        '###label.company###'        => 'company',
    ]),
], [
    'db_type'  => 'text',
    'default'  => 'all',
    'expanded' => 0,
    'multiple' => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'target', [
    'type_name'   => 'choice',
    'list_hidden' => 0,
    'search'      => 1,
    'label'       => 'translate:label.target',
    'placeholder' => '- ###action.select.please### -',
    'prio'        => $prio++,
    'choices'     => json_encode([
        'translate:label.cart_value'       => 'cart_value',
        'translate:label.order_quantities' => 'order_quantities',
    ]),
], [
    'db_type'    => 'text',
    'expanded'   => 0,
    'multiple'   => 0,
    'attributes' => '{"data-yform-edit-toggle-switch":"target"}',
]);

\Kreatif\Yform::ensureValueField($table, 'price', [
    'label'       => 'translate:label.at_least_price',
    'list_hidden' => 1,
    'search'      => 0,
], [
    'type_name'  => 'number',
    'db_type'    => '',
    'scale'      => 2,
    'precision'  => 10,
    'unit'       => '€',
    'prio'       => $prio++,
    'attributes' => '{"data-yform-edit-toggle":"target:cart_value"}',
]);

\Kreatif\Yform::ensureValueField($table, 'amount', [
    'label'       => 'translate:label.at_least_amount',
    'list_hidden' => 1,
    'search'      => 0,
], [
    'type_name'  => 'integer',
    'db_type'    => 'int',
    'prio'       => $prio++,
    'attributes' => '{"data-yform-edit-toggle":"target:order_quantities"}',
]);

\Kreatif\Yform::ensureValueField($table, 'action', [
    'type_name'   => 'choice',
    'list_hidden' => 0,
    'search'      => 1,
    'label'       => 'translate:action',
    'placeholder' => '- ###action.select.please### -',
    'prio'        => $prio++,
], [
    'db_type'    => 'text',
    'expanded'   => 0,
    'multiple'   => 0,
    'attributes' => '{"data-yform-edit-toggle-switch":"action"}',
    'choices'    => json_encode([
        'translate:coupon.percent_discount' => 'percent_discount',
        'translate:coupon.fixed_discount'   => 'fixed_discount',
        'translate:coupon.free_shipping'    => 'free_shipping',
        'translate:coupon.generate_code'    => 'coupon_code',
    ]),
]);

\Kreatif\Yform::ensureValueField($table, 'discount_percent', [
    'label'       => 'translate:label.discount',
    'list_hidden' => 1,
    'search'      => 0,
], [
    'type_name'  => 'integer',
    'db_type'    => 'int',
    'unit'       => '%',
    'prio'       => $prio++,
    'attributes' => '{"data-yform-edit-toggle":"action:percent_discount"}',
]);

\Kreatif\Yform::ensureValueField($table, 'discount_value', [
    'label'       => 'translate:label.discount',
    'list_hidden' => 1,
    'search'      => 0,
], [
    'type_name'  => 'number',
    'db_type'    => '',
    'scale'      => 2,
    'precision'  => 10,
    'unit'       => '€',
    'prio'       => $prio++,
    'attributes' => '{"data-yform-edit-toggle":"action:fixed_discount"}',
]);

\Kreatif\Yform::ensureValueField($table, 'discount_value', [
    'label'       => 'translate:label.discount',
    'list_hidden' => 1,
    'search'      => 0,
], [
    'type_name'  => 'number',
    'db_type'    => '',
    'scale'      => 2,
    'precision'  => 10,
    'unit'       => '€',
    'prio'       => $prio++,
    'attributes' => '{"data-yform-edit-toggle":"action:fixed_discount"}',
]);

\Kreatif\Yform::ensureValueField($table, 'coupon_code', [
    'type_name'   => 'choice',
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.coupon',
    'notice'      => 'translate:coupon.code_generate_notice',
    'prio'        => $prio++,
], [
    'db_type'    => 'text',
    'expanded'   => 0,
    'multiple'   => 0,
    'attributes' => '{"data-yform-edit-toggle":"action:coupon_code","data-yform-tools-select2":1}',
    'choices'    => 'SELECT id AS value, CONCAT(name_1, " [", code, "]") AS label FROM rex_shop_coupon ORDER BY id DESC',
]);

\Kreatif\Yform::ensureValueField($table, 'status', [
    'type_name'   => 'choice',
    'list_hidden' => 1,
    'search'      => 1,
    'label'       => 'translate:label.status',
    'prio'        => $prio++,
], [
    'db_type'  => 'text',
    'default'  => 1,
    'expanded' => 0,
    'multiple' => 0,
    'choices'  => json_encode([
        'translate:label.active'   => 1,
        'translate:label.inactive' => 0,
    ]),
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
$sql->setValue('name', 'translate:tablename.discount_group');
$sql->setValue('list_sortfield', 'id');
$sql->setValue('list_sortorder', 'DESC');
$sql->setValue('list_amount', 100);
$sql->setWhere(['table_name' => $table]);
$sql->update();

$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);

