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
$table = \FriendsOfREDAXO\Simpleshop\CustomerAddress::TABLE;


\Kreatif\Yform::ensureValueField($table, 'customer_id', 'be_manager_relation', [
    'list_hidden' => 1,
    'search'      => 0,
    'show_value'  => 1,
    'label'       => 'translate:label.customer_id',
    'prio'        => $prio++,
], [
    'type'         => 2,
    'empty_option' => 0,
    'empty_value'  => 'translate:error.link_customer_address',
    'field'        => 'id',
    'table'        => \FriendsOfREDAXO\Simpleshop\Customer::TABLE,
]);

\Kreatif\Yform::ensureValueField($table, 'name', 'list_name', [
    'prio'        => $prio++,
    'label'       => '###label.name###',
    'list_hidden' => 0,
    'search'      => 1,
], [
    'db_type' => 'none',
]);

\Kreatif\Yform::ensureValueField($table, 'ctype', 'choice', [
    'list_hidden' => 0,
    'search'      => 0,
    'label'       => '###label.customer_type###',
    'default'     => 'person',
    'attributes'  => '{"onchange":"Simpleshop.changeCType(this)","data-init-form-toggle":"1"}',
    'prio'        => $prio++,
], [
    'choices'  => '###label.person###=person,###label.company###=company',
    'db_type'  => 'text',
    'no_db'    => 0,
    'multiple' => 0,
    'expanded' => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'company_name', 'text', [
    'label'       => '###label.company_name###',
    'list_hidden' => 1,
    'search'      => 1,
    'attributes'  => '{"data-form-toggle":"company"}',
    'prio'        => $prio++,
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'firstname', 'text', [
    'label'       => '###label.firstname###',
    'list_hidden' => 1,
    'search'      => 1,
    'attributes'  => '{"data-form-toggle":"person"}',
    'prio'        => $prio++,
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'lastname', 'text', [
    'label'       => '###label.lastname###',
    'list_hidden' => 1,
    'search'      => 1,
    'attributes'  => '{"data-form-toggle":"person"}',
    'prio'        => $prio++,
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'fiscal_code', 'text', [
    'label'       => '###label.fiscal_code###',
    'list_hidden' => 1,
    'search'      => 1,
    'prio'        => $prio++,
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'vat_num', 'text', [
    'label'       => '###label.vat_short###',
    'list_hidden' => 1,
    'search'      => 1,
    'attributes'  => '{"data-form-toggle":"company"}',
    'prio'        => $prio++,
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'street', 'text', [
    'label'       => '###label.street###',
    'list_hidden' => 1,
    'search'      => 0,
    'prio'        => $prio++,
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'street_additional', 'text', [
    'label'       => '###label.address_additional###',
    'list_hidden' => 1,
    'search'      => 0,
    'prio'        => $prio++,
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'location', 'text', [
    'label'       => '###label.location###',
    'list_hidden' => 0,
    'search'      => 1,
    'prio'        => $prio++,
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'postal', 'text', [
    'label'       => '###label.postal###',
    'list_hidden' => 1,
    'search'      => 0,
    'prio'        => $prio++,
], [
    'db_type' => 'varchar(191)',
    'no_db'   => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'country', 'be_manager_relation', [
    'label'       => '###label.country###',
    'list_hidden' => 0,
    'search'      => 1,
    'prio'        => $prio++,
], [
    'db_type'      => 'int',
    'empty_value'  => 'translate:error.link_country',
    'table'        => \Kreatif\Model\Country::TABLE,
    'field'        => 'name_1',
    'empty_option' => 1,
    'type'         => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'status', 'choice', [
    'list_hidden' => 1,
    'search'      => 1,
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
$sql->setValue('prio', 50);
$sql->setWhere(['table_name' => $table]);
$sql->update();

$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);

