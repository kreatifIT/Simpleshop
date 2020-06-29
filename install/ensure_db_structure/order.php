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


\Kreatif\Yform::ensureValueField($table, 'order_functions', [], [
    'type_name'   => 'order_functions',
    'db_type'     => 'none',
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => '',
    'prio'        => $prio++,
]);

\Kreatif\Yform::ensureValueField($table, 'ref_order_id', [
    'type_name'   => 'hidden_input',
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.ref_order_label',
    'prio'        => $prio++,
], [
    'db_type' => 'int',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'customer_id', [
    'label'     => 'translate:label.customer',
    'type_name' => 'hidden_input',
    'prio'      => $prio++,
], [
    'db_type'     => 'int',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'status', [
    'label' => '###label.status###',
    'prio'  => $prio++,
], [
    'type_name'   => 'order_status_select',
    'db_type'     => 'varchar(191)',
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

\Kreatif\Yform::ensureValueField($table, 'invoice_num', [
    'label' => '###label.invoice_num###',
    'prio'  => $prio++,
], [
    'type_name'   => 'cst_shop_inputs',
    'db_type'     => 'varchar(191)',
    'list_hidden' => 0,
    'search'      => 1,
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'shipping_address_id', [
    'label'       => 'translate:label.customer',
    'empty_value' => '###label.shipping_address###',
    'prio'        => $prio++,
], [
    'type_name'    => 'customer_address',
    'db_type'      => 'int',
    'empty_option' => 0,
    'list_hidden'  => 0,
    'search'       => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'products', [
    'label' => 'translate:label.products',
    'prio'  => $prio++,
], [
    'type_name'   => 'order_products',
    'db_type'     => 'none',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'remarks', [
    'label' => '###label.remarks_infos###',
    'prio'  => $prio++,
], [
    'type_name'   => 'textarea',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'quantity', [
    'label' => '###label.amount###',
    'prio'  => $prio++,
], [
    'type_name'   => 'integer',
    'db_type'     => 'int',
    'list_hidden' => 1,
    'search'      => 0,
    'default'     => 0,
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'initial_total', [
    'label' => 'translate:label.initial_total',
    'prio'  => $prio++,
], [
    'type_name'   => 'number',
    'db_type'     => '',
    'list_hidden' => 1,
    'search'      => 0,
    'default'     => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'net_prices', [
    'label' => 'translate:label.net_prices',
    'prio'  => $prio++,
], [
    'type_name'   => 'data_output',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'shipping_costs', [
    'label' => '###label.shipping_costs###',
    'prio'  => $prio++,
], [
    'type_name'   => 'number',
    'db_type'     => '',
    'list_hidden' => 1,
    'search'      => 1,
    'default'     => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'manual_discount', [
    'label' => 'translate:label.manual_discount',
    'prio'  => $prio++,
], [
    'type_name'   => 'number',
    'db_type'     => '',
    'list_hidden' => 1,
    'search'      => 1,
    'default'     => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
]);

\Kreatif\Yform::ensureValueField($table, 'discount', [
    'label' => 'translate:label.discount_sum',
    'prio'  => $prio++,
], [
    'type_name'   => 'number',
    'db_type'     => '',
    'list_hidden' => 1,
    'search'      => 1,
    'default'     => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'taxes', [
    'label' => '###label.tax###',
    'prio'  => $prio++,
], [
    'type_name'   => 'data_output',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'brut_prices', [
    'label' => 'translate:settings.brutto_prices',
    'prio'  => $prio++,
], [
    'type_name'   => 'data_output',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'total', [
    'label' => '###label.total_sum###',
    'prio'  => $prio++,
], [
    'type_name'   => 'number',
    'db_type'     => '',
    'list_hidden' => 0,
    'search'      => 1,
    'default'     => 0,
    'scale'       => 2,
    'precision'   => 10,
    'unit'        => '€',
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'customer_data', [
    'label' => 'translate:label.customer_data',
    'prio'  => $prio++,
], [
    'type_name'   => 'model_data',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'invoice_address', [
    'label' => '###label.invoice_address###',
    'prio'  => $prio++,
], [
    'type_name'   => 'model_data',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'shipping_address', [
    'label' => '###label.shipping_address###',
    'prio'  => $prio++,
], [
    'type_name'   => 'model_data',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'promotions', [
    'label' => '###label.promotions###',
    'prio'  => $prio++,
], [
    'type_name'   => 'model_data',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'payment', [
    'label' => '###label.payment_method###',
    'prio'  => $prio++,
], [
    'type_name'   => 'model_data',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'shipping', [
    'label' => '###label.shipping_method###',
    'prio'  => $prio++,
], [
    'type_name'   => 'model_data',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'shipping_key', [
    'label' => 'translate:label.shipping_key',
    'prio'  => $prio++,
], [
    'type_name'   => 'text',
    'db_type'     => 'varchar(191)',
    'list_hidden' => 1,
    'search'      => 1,
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'extras', [
    'label' => 'Extras',
    'prio'  => $prio++,
], [
    'type_name'   => 'model_data',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'ip_address', [
    'label' => 'IP',
    'prio'  => $prio++,
], [
    'type_name'   => 'text',
    'db_type'     => 'varchar(191)',
    'list_hidden' => 1,
    'search'      => 0,
    'attributes'  => '{"readonly":"readonly"}',
]);

\Kreatif\Yform::ensureValueField($table, 'notes', [
    'label' => 'translate:label.internal_notes',
    'prio'  => $prio++,
], [
    'type_name'   => 'textarea',
    'db_type'     => 'text',
    'list_hidden' => 1,
    'search'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'createdate', [
    'label' => 'translate:label.created_at',
    'prio'  => $prio++,
], [
    'type_name'   => 'datestamp',
    'db_type'     => 'datetime',
    'list_hidden' => 0,
    'search'      => 0,
    'format'      => 'Y-m-d H:i:s',
    'only_empty'  => 1,
    'show_value'  => 1,
]);

\Kreatif\Yform::ensureValueField($table, 'updatedate', [
    'label' => 'translate:label.updated_at',
    'prio'  => $prio++,
], [
    'type_name'   => 'datestamp',
    'db_type'     => 'datetime',
    'list_hidden' => 1,
    'search'      => 0,
    'format'      => 'Y-m-d H:i:s',
    'only_empty'  => 0,
    'show_value'  => 1,
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

$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);
