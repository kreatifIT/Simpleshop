<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 24.02.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


$sql = rex_sql::factory();
$sql->setQuery('SET FOREIGN_KEY_CHECKS = 0');

try {
    rex_sql_table::get('rex_shop_customer_address')
        ->ensureColumn(new rex_sql_column('ctype', 'varchar(191)', true))
        ->ensureColumn(new rex_sql_column('vat_num', 'varchar(191)', true))
        ->ensureColumn(new rex_sql_column('fiscal_code', 'varchar(191)', true))
        ->ensure();

    $stmt      = \FriendsOfREDAXO\Simpleshop\Customer::query();
    $customers = $stmt->find();

    rex_sql_table::get('rex_shop_customer')
        ->ensureColumn(new rex_sql_column('invoice_address_id', 'int'))
        ->removeColumn('company_name')
        ->removeColumn('lastname')
        ->removeColumn('firstname')
        ->removeColumn('ctype')
        ->removeColumn('vat_num')
        ->removeColumn('fiscal_code')
        ->ensure();

    $item = $sql->getArray('SELECT id FROM rex_yform_field WHERE table_name = :table AND type_id = :type AND name = :name', ['table' => 'rex_shop_customer', 'type' => 'value', 'name' => 'invoice_address_id'])[0];
    if (!$item) {
        $sql->setTable('rex_yform_field');
        $sql->setValue('table_name', 'rex_shop_customer');
        $sql->setValue('prio', 5);
        $sql->setValue('type_id', 'value');
        $sql->setValue('type_name', 'be_manager_relation');
        $sql->setValue('db_type', 'int');
        $sql->setValue('list_hidden', 0);
        $sql->setValue('search', 1);
        $sql->setValue('name', 'invoice_address_id');
        $sql->setValue('label', 'Kunden-Adresse');
        $sql->setValue('type', 2);
        $sql->setValue('empty_value', 'Bitte eine Adresse zuweisen');
        $sql->setValue('table', 'rex_shop_customer_address');
        $sql->setValue('field', 'company_name," ",firstname," ",lastname');
        $sql->setValue('empty_option', 0);
        $sql->insert();
    }
    $item = $sql->getArray('SELECT id FROM rex_yform_field WHERE table_name = :table AND type_id = :type AND name = :name', ['table' => 'rex_shop_customer_address', 'type' => 'validate', 'name' => 'fiscal_code'])[0];
    if (!$item) {
        $sql->setTable('rex_yform_field');
        $sql->setValue('table_name', 'rex_shop_customer_address');
        $sql->setValue('name', 'fiscal_code');
        $sql->setValue('type_id', 'validate');
        $sql->setValue('list_hidden', 1);
        $sql->setValue('search', 0);
        $sql->insert();
    }
    $item = $sql->getArray('SELECT id FROM rex_yform_field WHERE table_name = :table AND type_id = :type AND name = :name', ['table' => 'rex_shop_customer_address', 'type' => 'validate', 'name' => 'vat_num'])[0];
    if (!$item) {
        $sql->setTable('rex_yform_field');
        $sql->setValue('table_name', 'rex_shop_customer_address');
        $sql->setValue('name', 'vat_num');
        $sql->setValue('type_id', 'validate');
        $sql->setValue('list_hidden', 1);
        $sql->setValue('search', 0);
        $sql->insert();
    }

    $sql->setQuery('
        DELETE FROM rex_yform_field WHERE table_name = "rex_shop_customer" AND name = "company_name";
        DELETE FROM rex_yform_field WHERE table_name = "rex_shop_customer" AND name = "lastname";
        DELETE FROM rex_yform_field WHERE table_name = "rex_shop_customer" AND name = "firstname";
        DELETE FROM rex_yform_field WHERE table_name = "rex_shop_customer" AND name = "ctype";
        
        UPDATE rex_yform_table SET list_sortfield = "id", list_amount = 100, list_sortorder = "DESC" WHERE table_name = "rex_shop_customer";
        UPDATE rex_yform_field SET field = "id" WHERE `table` = "rex_shop_customer";
        UPDATE rex_yform_field SET field = "company_name," ",firstname," ",lastname" WHERE `table` = "rex_shop_customer_address";
        
        UPDATE rex_yform_field SET type_name = "choice", db_type = "text", options="", `default`="person", `table_name`="rex_shop_customer_address", choices="{\"###label.private_person###\":\"person\",\"###label.company###\":\"company\"}" WHERE table_name = "rex_shop_customer" AND type_id = "value" AND name = "ctype";
        UPDATE rex_yform_field SET `table_name`="rex_shop_customer_address" WHERE table_name = "rex_shop_customer" AND name = "fiscal_code";
        UPDATE rex_yform_field SET `table_name`="rex_shop_customer_address" WHERE table_name = "rex_shop_customer" AND name = "vat_num";
        
        UPDATE rex_yform_field SET db_type="varchar(191)" WHERE table_name = "rex_shop_customer_address" AND type_id = "value" AND name = "fiscal_code";
        UPDATE rex_yform_field SET db_type="varchar(191)" WHERE table_name = "rex_shop_customer_address" AND type_id = "value" AND name = "vat_num";
        UPDATE rex_yform_field SET type_name="empty", message="###error.field.empty###", validate_type=0 WHERE table_name = "rex_shop_customer_address" AND type_id = "validate" AND name = "firsname";
        UPDATE rex_yform_field SET type_name="empty", message="###error.field.empty###", validate_type=0 WHERE table_name = "rex_shop_customer_address" AND type_id = "validate" AND name = "lastname";
        UPDATE rex_yform_field SET type_name="customfunction", message="###error.field.empty###", `function`=:customvalidation, params="{\"method\":\"empty-if\",\"dependencies\":[{\"field\":\"ctype\",\"value\":\"company\"}]}", validate_type=0 WHERE table_name = "rex_shop_customer_address" AND type_id = "validate" AND name = "company_name";
        UPDATE rex_yform_field SET type_name="customfunction", message="###error.field.fiscal_code_not_valid####", `function`=:customvalidation, params="{\"method\":\"fiscal_code\",\"dependencies\":[{\"field\":\"ctype\",\"value\":\"person\"},{\"field\":\"country\",\"value\":93,\"valueFrom\":\"post\"}]}", validate_type=0 WHERE table_name = "rex_shop_customer_address" AND type_id = "validate" AND name = "fiscal_code";
        UPDATE rex_yform_field SET type_name="customfunction", message="###error.field.vat_not_valid####", `function`=:customvalidation, params="{\"method\":\"vat_num\",\"dependencies\":[{\"field\":\"ctype\",\"value\":\"company\"}]}", validate_type=0 WHERE table_name = "rex_shop_customer_address" AND type_id = "validate" AND name = "vat_num";
    ', ['customvalidation' => '\FriendsOfREDAXO\Simpleshop\Customer::customValidateField']);

    $customers = $sql->getArray('SELECT id FROM rex_shop_customer WHERE invoice_address_id = 0');

    foreach ($customers as $item) {
        $address = $sql->getArray('SELECT id FROM rex_shop_customer_address WHERE customer_id = :id ORDER BY id DESC LIMIT 1', ['id' => $item['id']])[0];

        if ($address) {
            $sql->setTable('rex_shop_customer');
            $sql->setValue('invoice_address_id', $address['id']);
            $sql->setWhere('id = :id', ['id' => $item['id']]);
            $sql->update();
        }
    }
}
finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}