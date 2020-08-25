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
$table = \FriendsOfREDAXO\Simpleshop\Product::TABLE;

\Kreatif\Yform::ensureValueField($table, 'product_functions', 'product_functions', [
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

    \Kreatif\Yform::ensureValueField($table, 'description_' . $lang->getId(), 'textarea', [
        'label'       => 'translate:label.description',
        'list_hidden' => 1,
        'search'      => 0,
        'attributes'  => '{"class":"tinyMCEEditor"}',
        'prio'        => $prio++,
    ], [
        'db_type' => 'text',
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

\Kreatif\Yform::ensureValueField($table, 'type', 'hidden_input', [
    'label'       => 'translate:label.product_type',
    'prio'        => $prio++,
    'list_hidden' => 1,
    'search'      => 0,
    'default'     => 'product',
    'choices'     => json_encode([
        'translate:label.product' => 'product',
        '###label.giftcard###'    => 'giftcard',
    ]),
], [
    'db_type'  => 'varchar(191)',
    'no_db'    => 0,
    'multiple' => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'code', 'text', [
    'list_hidden' => 0,
    'search'      => 1,
    'label'       => '###label.product_code###',
    'prio'        => $prio++,
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValidateField($table, 'code', 'empty', [
    'prio' => $prio++,
], [
    'message' => 'translate:error.empty_product_code',
]);

\Kreatif\Yform::ensureValidateField($table, 'code', 'unique', [
    'prio' => $prio++,
], [
    'message' => 'translate:error.unique_product_code',
]);

\Kreatif\Yform::ensureValueField($table, 'inventory', 'choice', [
    'label'       => 'translate:label.inventory',
    'prio'        => $prio++,
    'list_hidden' => 1,
    'search'      => 0,
    'default'     => 'N',
    'notice'      => 'translate:label.inventory_notice',
    'choices'     => json_encode([
        'translate:label.not_tack_inventory' => 'N',
        'translate:label.tack_inventory'     => 'F',
    ]),
], [
    'db_type'  => 'varchar(191)',
    'no_db'    => 0,
    'multiple' => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'amount', 'integer', [
    'list_hidden' => 1,
    'search'      => 0,
    'default'     => 1,
    'label'       => 'translate:label.amount',
    'prio'        => $prio++,
], [
    'db_type' => 'int',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValidateField($table, 'amount', 'empty', [
    'prio' => $prio++,
], [
    'message' => '###error.field.empty###',
]);

\Kreatif\Yform::ensureValueField($table, 'b2b_sale_amount', 'hidden_input', [
    'list_hidden' => 1,
    'search'      => 0,
    'default'     => 1,
    'label'       => 'translate:label.b2b_amount',
    'prio'        => $prio++,
], [
    'db_type' => 'int',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'price', 'number', [
    'list_hidden' => 1,
    'search'      => 0,
    'placeholder' => '€',
    'label'       => 'translate:label.net_single_price',
    'prio'        => $prio++,
], [
    'scale'     => 3,
    'precision' => 10,
    'default'   => 0,
    'no_db'     => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'reduced_price', 'hidden_input', [
    'list_hidden' => 1,
    'search'      => 0,
    'placeholder' => '€',
    'label'       => 'translate:label.reduced_net_single_price',
    'prio'        => $prio++,
], [
    'scale'     => 3,
    'precision' => 10,
    'default'   => 0,
    'no_db'     => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'images', 'be_manager_relation', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => '###label.tax###',
    'prio'        => $prio++,
], [
    'db_type'      => 'int',
    'empty_value'  => 'translate:error.link_tax',
    'table'        => \FriendsOfREDAXO\Simpleshop\Tax::TABLE,
    'field'        => 'tax',
    'empty_option' => 0,
    'type'         => 0,
    'no_db'        => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'images', 'be_media', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.images',
    'prio'        => $prio++,
], [
    'db_type'  => 'text',
    'preview'  => 1,
    'multiple' => 1,
]);

\Kreatif\Yform::ensureValueField($table, 'length', 'hidden_input', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.product_length',
    'prio'        => $prio++,
], [
    'scale'     => 2,
    'precision' => 10,
    'default'   => 0,
    'no_db'     => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'width', 'hidden_input', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.product_width',
    'prio'        => $prio++,
], [
    'scale'     => 2,
    'precision' => 10,
    'default'   => 0,
    'no_db'     => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'height', 'hidden_input', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.product_height',
    'prio'        => $prio++,
], [
    'scale'     => 2,
    'precision' => 10,
    'default'   => 0,
    'no_db'     => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'weight', 'hidden_input', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.product_weight',
    'prio'        => $prio++,
], [
    'scale'     => 2,
    'precision' => 10,
    'default'   => 0,
    'no_db'     => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'features', 'hidden_input', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.features',
    'prio'        => $prio++,
], [
    'table'        => \FriendsOfREDAXO\Simpleshop\FeatureValue::TABLE,
    'field'        => 'name_1',
    'empty_option' => 1,
    'type'         => 3,
]);

\Kreatif\Yform::ensureValueField($table, 'related_products', 'product_variant_select', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.related_products',
    'attributes'  => '{"data-minimum-input-length":2}',
    'prio'        => $prio++,
], [
    'multiple' => 1,
    'no_db'    => 0,
    'db_type'  => 'text',
]);

\Kreatif\Yform::ensureValueField($table, 'status', 'choice', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.status',
    'prio'        => $prio++,
], [
    'db_type'  => 'int',
    'default'  => 1,
    'expanded' => 0,
    'multiple' => 0,
    'choices'  => json_encode([
        'translate:label.active'   => 1,
        'translate:label.inactive' => 0,
    ]),
]);

\Kreatif\Yform::ensureValueField($table, 'prio', 'prio', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => 'translate:label.priority',
    'prio'        => $prio++,
], [
    'fields'  => 'name_1',
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

