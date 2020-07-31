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
$langs = array_values(rex_clang::getAll());
$table = \FriendsOfREDAXO\Simpleshop\Coupon::TABLE;

\Kreatif\Yform::ensureValueField($table, 'coupon_functions', 'coupon_functions', [
    'prio' => $prio++,
], [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => '',
    'db_type'     => 'none',
]);

\Kreatif\Yform::ensureValueField($table, 'tab_start', 'tab_start', [
    'prio' => $prio++,
], [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => '',
    'db_type'     => 'none',
]);

foreach ($langs as $index => $lang) {
    \Kreatif\Yform::ensureValueField($table, 'name_' . $lang->getId(), 'text', [
        'label'       => 'translate:label.designation',
        'list_hidden' => $index == 0 ? 0 : 1,
        'search'      => $index == 0 ? 1 : 0,
        'prio'        => $prio++,
    ], [
        'db_type' => 'varchar(191)',
    ]);

    if (($index + 1) < count($langs)) {
        \Kreatif\Yform::ensureValueField($table, 'tab_break_' . $lang->getId(), 'tab_break', [
            'prio' => $prio++,
        ], [
            'list_hidden' => 1,
            'search'      => 0,
            'label'       => '',
            'db_type'     => 'none',
        ]);
    }
}

\Kreatif\Yform::ensureValueField($table, 'tab_end', 'tab_end', [
    'prio' => $prio++,
], [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => '',
    'db_type'     => 'none',
]);

$prio += 20;

\Kreatif\Yform::ensureValueField($table, 'is_multi_use', 'choice', [
    'list_hidden' => 0,
    'search'      => 0,
    'label'       => 'translate:coupon.use_settings',
    'choices'     => '', // these will be set in the simpleshop advanced settings
    'prio'        => $prio++,
], [
    'db_type'  => 'int',
    'default'  => 0,
    'no_db'    => 0,
    'multiple' => 0,
    'expanded' => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'start_time', 'date', [
    'list_hidden'  => 0,
    'search'       => 0,
    'current_date' => 1,
    'label'        => 'translate:label.start_date',
    'widget'       => 'input:text',
    'format'       => 'DD.MM.YYYY',
    'attributes'   => '{"data-yform-tools-datepicker":"DD.MM.YYYY","autocomplete":"off"}',
    'prio'         => $prio++,
], [
    'db_type' => 'date',
    'default' => 0,
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'end_date', 'date', [
    'list_hidden'  => 1,
    'search'       => 0,
    'current_date' => 0,
    'label'        => 'translate:label.end_date',
    'widget'       => 'input:text',
    'format'       => 'DD.MM.YYYY',
    'attributes'   => '{"data-yform-tools-datepicker":"DD.MM.YYYY","autocomplete":"off"}',
    'notice'       => 'translate:label.coupon_end_date',
    'prio'         => $prio++,
], [
    'db_type' => 'date',
    'default' => 0,
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'action', 'choice', [
    'list_hidden' => 1,
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
    ]),
]);

\Kreatif\Yform::ensureValueField($table, 'discount_value', 'number', [
    'list_hidden' => 1,
    'search'      => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
    'label'       => 'translate:coupon.fixed_discount',
    'prio'        => $prio++,
], [
    'db_type'    => '',
    'no_db'      => 0,
    'attributes' => '{"data-yform-edit-toggle":"action:fixed_discount"}',
]);

\Kreatif\Yform::ensureValueField($table, 'discount_percent', 'number', [
    'list_hidden' => 1,
    'search'      => 0,
    'scale'       => 1,
    'precision'   => 4,
    'unit'        => '%',
    'label'       => 'translate:coupon.percent_discount',
    'prio'        => $prio++,
], [
    'db_type'    => '',
    'no_db'      => 0,
    'attributes' => '{"data-yform-edit-toggle":"action:percent_discount"}',
]);

\Kreatif\Yform::ensureValueField($table, 'prefix', 'text', [
    'list_hidden' => 1,
    'search'      => 1,
    'label'       => 'translate:label.prefix',
    'attributes'  => '{"placeholder":"AA"}',
    'prio'        => $prio++,
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'code', 'coupon_code', [
    'list_hidden' => 0,
    'search'      => 1,
    'label'       => 'Code',
    'notice'      => 'translate:label.coupon_code_notice',
    'prio'        => $prio++,
], [
    'db_type'    => 'varchar(191)',
    'attributes' => '{"readonly":"readonly"}',
    'no_db'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'status', 'choice', [
    'label'   => '###label.status###',
    'prio'    => $prio++,
    'default' => 'usable',
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 1,
    'multiple'    => 0,
    'choices'     => json_encode([
        '###shop.coupon_status_inactive###'      => 'inactive',
        '###shop.coupon_status_usable###'        => 'usable',
        '###shop.coupon_status_givenaway###'     => 'givenaway',
        '###shop.coupon_status_autogenerated###' => 'autogenerated',
    ]),
]);

\Kreatif\Yform::ensureValueField($table, 'orders', 'data_output', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:coupon.use_info',
    'prio'        => $prio++,
], [
    'db_type' => 'text',
    'no_db'   => 0,
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
$sql->setValue('prio', 13);
$sql->setWhere(['table_name' => $table]);
$sql->update();

$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);

