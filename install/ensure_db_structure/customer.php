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
$table = \FriendsOfREDAXO\Simpleshop\Customer::TABLE;

\Kreatif\Yform::ensureValueField($table, 'name', 'list_name', [
    'prio'        => $prio++,
    'label'       => '###label.name###',
    'list_hidden' => 0,
    'search'      => 1,
], [
    'db_type' => 'none',
]);

\Kreatif\Yform::ensureValueField($table, 'email', 'email', [
    'list_hidden' => 0,
    'search'      => 1,
    'label'       => '###label.email###',
    'prio'        => $prio++,
], [
    'db_type'   => 'varchar(191)',
    'no_db'     => 0,
]);

\Kreatif\Yform::ensureValidateField($table, 'email', 'empty', [
    'prio' => $prio++,
], [
    'message'      => '###error.field.empty###',
]);

\Kreatif\Yform::ensureValidateField($table, 'email', 'email', [
    'prio' => $prio++,
], [
    'message'   => '###error.email_not_valid###',
]);

\Kreatif\Yform::ensureValidateField($table, 'email', 'unique', [
    'prio' => $prio++,
], [
    'message'      => '###error.email_already_exists###',
    'table'        => $table,
    'empty_option' => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'password', 'text', [
    'label'      => '###label.password###',
    'notice'     => '###label.keep_password_notice###',
    'attributes' => '{"type":"password"}',
    'prio'       => $prio++,
], [
    'list_hidden' => 1,
    'search'      => 0,
    'db_type'     => 'varchar(191)',
    'no_db'       => 1,
]);

\Kreatif\Yform::ensureValueField($table, 'password_hash', 'hashvalue', [
    'label' => 'Passwort Hash',
    'prio'  => $prio++,
], [
    'list_hidden' => 1,
    'search'      => 0,
    'no_db'       => 0,
    'field'       => 'password',
    'db_type'     => 'varchar(191)',
    'function'    => 'sha1',
    'salt'        => 'UYD7FFtMLdqr4ZujqwED',
]);


\Kreatif\Yform::ensureValueField($table, 'lang_id', 'choice', [
    'list_hidden' => 1,
    'search'      => 0,
    'label'       => '###label.language###',
    'default'     => 1,
    'prio'        => $prio++,
], [
    'choices'   => 'SELECT id AS value, name AS label FROM rex_clang',
    'db_type'   => 'int',
    'no_db'     => 0,
    'multiple'  => 0,
    'expanded'  => 0,
]);


\Kreatif\Yform::ensureValueField($table, 'addresses', 'be_manager_relation', [
    'label' => '###label.addresses###',
    'prio'  => $prio++,
], [
    'db_type'      => 'int',
    'list_hidden'  => 0,
    'search'       => 0,
    'no_db'        => '',
    'type'         => 4,
    'table'        => 'rex_shop_customer_address',
    'field'        => 'customer_id',
    'empty_option' => 1,
]);

\Kreatif\Yform::ensureValueField($table, 'invoice_address_id', 'hidden_input', [
    'label' => '',
    'prio'  => $prio++,
], [
    'db_type'     => 'int',
    'list_hidden' => 1,
    'search'      => 0,
    'no_db'       => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'lastlogin', 'datestamp', [
    'label'       => 'translate:label.last_login',
    'list_hidden' => 0,
    'search'      => 0,
    'show_value'  => 1,
    'prio'        => $prio++,
], [
    'db_type'    => 'datetime',
    'format'     => 'Y-m-d H:i:s',
    'only_empty' => 2,
    'no_db'      => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'hash', 'hidden_input', [
    'label' => '',
    'prio'  => $prio++,
], [
    'db_type'     => 'varchar(191)',
    'list_hidden' => 1,
    'search'      => 0,
    'no_db'       => 0,
]);

\Kreatif\Yform::ensureValueField($table, 'activationdate', 'datestamp', [
    'list_hidden' => 0,
    'search'      => 0,
    'show_value'  => 1,
    'label'       => 'translate:label.actived_on',
    'prio'        => $prio++,
], [
    'db_type'    => 'datetime',
    'format'     => 'Y-m-d H:i:s',
    'only_empty' => 2,
    'no_db'      => 0,
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
$sql->setValue('prio', 12);
$sql->setWhere(['table_name' => $table]);
$sql->update();

$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);

