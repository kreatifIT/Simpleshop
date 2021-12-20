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
$table = \FriendsOfREDAXO\Simpleshop\Order::TABLE;


\Kreatif\Yform::ensureValueField($table, 'order_functions', 'order_functions', [], [
    'db_type'     => 'none',
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => '',
    'prio'        => $prio++,
]);

\Kreatif\Yform::ensureValueField($table, 'ref_order_id', 'hidden_input', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.ref_order_label',
    'prio'        => $prio++,
], [
    'db_type' => 'int',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'customer_id', 'hidden_input', [
    'label'     => 'translate:label.customer',
    'prio'      => $prio++,
], [
    'db_type'     => 'int',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'status', 'order_status_select', [
    'label' => '###label.status###',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 1,
    'multiple'    => 0,
    'options'     => implode(',', [
        '###shop.order_status_OP###=OP',
        '###shop.order_status_IP###=IP',
        '###shop.order_status_FA###=FA',
        '###shop.order_status_SH###=SH',
        '###shop.order_status_CA###=CA',
        '###shop.order_status_CL###=CL',
        '###shop.order_status_CL###=CN',
    ]),
]);

\Kreatif\Yform::ensureValueField($table, 'invoice_num', 'cst_shop_inputs', [
    'label' => '###label.invoice_num###',
    'prio'  => $prio++,
], [
    'db_type'     => 'int',
    'list_hidden' => 0,
    'search'      => 1,
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'shipping_address_id', 'customer_address', [
    'label'       => 'translate:label.customer',
    'empty_value' => '###label.shipping_address###',
    'prio'        => $prio++,
], [
    'db_type'      => 'int',
    'empty_option' => 0,
    'list_hidden'  => 0,
    'search'       => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'products', 'order_products', [
    'label' => 'translate:label.products',
    'prio'  => $prio++,
], [
    'db_type'     => 'none',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'remarks', 'textarea', [
    'label' => '###label.remarks_infos###',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'quantity', 'integer', [
    'label' => '###label.amount###',
    'prio'  => $prio++,
], [
    'db_type'     => 'int',
    'list_hidden' => 1,
    'search'      => 0,
    'default'     => 0,
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'initial_total', 'number', [
    'label' => 'translate:label.initial_total',
    'prio'  => $prio++,
], [
    'db_type'     => '',
    'list_hidden' => 1,
    'search'      => 0,
    'default'     => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'net_prices', 'data_output', [
    'label' => 'translate:label.net_prices',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'shipping_costs', 'number', [
    'label' => '###label.shipping_costs###',
    'prio'  => $prio++,
], [
    'db_type'     => '',
    'list_hidden' => 1,
    'search'      => 1,
    'default'     => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'manual_discount', 'number', [
    'label' => 'translate:label.manual_discount',
    'prio'  => $prio++,
], [
    'db_type'     => '',
    'list_hidden' => 1,
    'search'      => 1,
    'default'     => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
]);

\Kreatif\Yform::ensureValueField($table, 'discount', 'number', [
    'label' => 'translate:label.discount_sum',
    'prio'  => $prio++,
], [
    'db_type'     => '',
    'list_hidden' => 1,
    'search'      => 1,
    'default'     => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'taxes', 'data_output', [
    'label' => '###label.tax###',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'brut_prices', 'data_output', [
    'label' => 'translate:settings.brutto_prices',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'total', 'number', [
    'label' => '###label.total_sum###',
    'prio'  => $prio++,
], [
    'db_type'     => '',
    'list_hidden' => 0,
    'search'      => 1,
    'default'     => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'customer_data', 'data_output', [
    'label' => 'translate:label.customer_data',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'invoice_address', 'data_output', [
    'label' => '###label.invoice_address###',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'shipping_address', 'data_output', [
    'label' => '###label.shipping_address###',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'promotions', 'data_output', [
    'label' => '###label.promotions###',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'payment', 'data_output', [
    'label' => '###label.payment_method###',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'shipping', 'data_output', [
    'label' => '###label.shipping_method###',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'shipping_key', 'text', [
    'label' => 'translate:label.shipping_key',
    'prio'  => $prio++,
], [
    'db_type'     => 'varchar(191)',
    'list_hidden' => 1,
    'search'      => 1,
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'shipping_tracking_url', 'hidden_input', [
    'label'     => 'Tracking-Url',
    'prio'      => $prio++,
], [
     'db_type'     => 'varchar(191)',
     'list_hidden' => 1,
     'search'      => 0,
 ]);

\Kreatif\Yform::ensureValueField($table, 'shipping_info_sent', 'datestamp', [
    'label' => 'Shipping info sent via mail',
    'prio'  => $prio++,
], [
     'db_type'     => 'datetime',
     'list_hidden' => 1,
     'search'      => 0,
     'format'      => 'Y-m-d H:i:s',
     'only_empty'  => 2,
     'show_value'  => 1,
]);

\Kreatif\Yform::ensureValueField($table, 'extras', 'data_output', [
    'label' => 'Extras',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'ip_address', 'text', [
    'label' => 'IP',
    'prio'  => $prio++,
], [
    'db_type'     => 'varchar(191)',
    'list_hidden' => 1,
    'search'      => 0,
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'notes', 'textarea', [
    'label' => 'translate:label.internal_notes',
    'prio'  => $prio++,
], [
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'createdate', 'datestamp', [
    'label' => 'translate:label.created_at',
    'prio'  => $prio++,
], [
    'db_type'     => 'datetime',
    'list_hidden' => 0,
    'search'      => 0,
    'format'      => 'Y-m-d H:i:s',
    'only_empty'  => 1,
    'show_value'  => 1,
]);

\Kreatif\Yform::ensureValueField($table, 'updatedate', 'datestamp', [
    'label' => 'translate:label.updated_at',
    'prio'  => $prio++,
], [
    'db_type'     => 'datetime',
    'list_hidden' => 1,
    'search'      => 0,
    'format'      => 'Y-m-d H:i:s',
    'only_empty'  => 0,
    'show_value'  => 1,
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
$sql->setValue('prio', 11);
$sql->setWhere(['table_name' => $table]);
$sql->update();

$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);
