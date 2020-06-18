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
        ->ensureColumn(new rex_sql_column('customer_id', 'int(11)'), 'id')
        ->ensureColumn(new rex_sql_column('ctype', 'varchar(191)', true), 'customer_id')
        ->ensureColumn(new rex_sql_column('fiscal_code', 'varchar(191)', true), 'country')
        ->ensureColumn(new rex_sql_column('vat_num', 'varchar(191)', true), 'fiscal_code')
        ->ensure();

    rex_sql_table::get('rex_shop_customer')
        ->ensureColumn(new rex_sql_column('invoice_address_id', 'int(11)'), 'id')
        ->ensure();

    $query     = \FriendsOfREDAXO\Simpleshop\Customer::query();
    $customers = $query->find();

    // move customer data to customer address
    foreach ($customers as $customer) {
        $query = \FriendsOfREDAXO\Simpleshop\CustomerAddress::query();
        $query->where('customer_id', $customer->getId());
        $query->orderBy('createdate', 'asc');
        $_cadress = $query->findOne();


        $sql->setTable(\FriendsOfREDAXO\Simpleshop\CustomerAddress::TABLE);
        $sql->setValue('ctype', $customer->getValue('ctype'));
        $sql->setValue('fiscal_code', $customer->getValue('fiscal_code'));
        $sql->setValue('vat_num', $customer->getValue('vat_num'));

        if ($_cadress) {
            $sql->setWhere(['customer_id' => $customer->getId()]);
            $sql->update();
            $addressId = $_cadress->getId();
        } else {
            $sql->setValue('firstname', $customer->getValue('firstname'));
            $sql->setValue('lastname', $customer->getValue('lastname'));
            $sql->setValue('company_name', $customer->getValue('company_name'));
            $sql->setValue('customer_id', $customer->getId());
            $sql->setValue('lang_id', $customer->getValue('lang_id'));
            $sql->setValue('status', 1);
            $sql->setValue('createdate', date('Y-m-d H:i:s'));
            $sql->setValue('updatedate', date('Y-m-d H:i:s'));
            $sql->insert();
            $addressId = $sql->getLastId();
        }

        if($customer->getValue('invoice_address_id') == 0 || $customer->getValue('invoice_address_id') == '') {
            $sql->setTable(\FriendsOfREDAXO\Simpleshop\Customer::TABLE);
            $sql->setValue('invoice_address_id', $addressId);
            $sql->setWhere(['id' => $customer->getId()]);
            $sql->update();
        }
    }

    $item = $sql->getArray('SELECT id FROM rex_yform_field WHERE table_name = :table AND type_id = :type AND name = :name', ['table' => 'rex_shop_customer', 'type' => 'value', 'name' => 'name'])[0];
    if (!$item) {
        $sql->setTable('rex_yform_field');
        $sql->setValue('table_name', 'rex_shop_customer');
        $sql->setValue('prio', 1);
        $sql->setValue('type_id', 'value');
        $sql->setValue('type_name', 'list_name');
        $sql->setValue('db_type', 'none');
        $sql->setValue('list_hidden', 0);
        $sql->setValue('search', 0);
        $sql->setValue('name', 'name');
        $sql->setValue('label', 'Name');
        $sql->insert();
    }
    $item = $sql->getArray('SELECT id FROM rex_yform_field WHERE table_name = :table AND type_id = :type AND name = :name', ['table' => 'rex_shop_customer', 'type' => 'value', 'name' => 'invoice_address_id'])[0];
    if (!$item) {
        $sql->setTable('rex_yform_field');
        $sql->setValue('table_name', 'rex_shop_customer');
        $sql->setValue('prio', 2);
        $sql->setValue('type_id', 'value');
        $sql->setValue('type_name', 'hidden_input');
        $sql->setValue('db_type', 'int');
        $sql->setValue('list_hidden', 1);
        $sql->setValue('search', 0);
        $sql->setValue('no_db', 0);
        $sql->setValue('name', 'invoice_address_id');
        $sql->insert();
    }
    $item = $sql->getArray('SELECT id FROM rex_yform_field WHERE table_name = :table AND type_id = :type AND name = :name', ['table' => 'rex_shop_customer_address', 'type' => 'value', 'name' => 'name'])[0];
    if (!$item) {
        $sql->setTable('rex_yform_field');
        $sql->setValue('table_name', 'rex_shop_customer_address');
        $sql->setValue('prio', 2);
        $sql->setValue('type_id', 'value');
        $sql->setValue('type_name', 'list_name');
        $sql->setValue('db_type', 'none');
        $sql->setValue('list_hidden', 0);
        $sql->setValue('search', 0);
        $sql->setValue('name', 'name');
        $sql->setValue('label', 'Name');
        $sql->insert();
    }
    $item = $sql->getArray('SELECT id FROM rex_yform_field WHERE table_name = :table AND type_id = :type AND name = :name', ['table' => 'rex_shop_customer_address', 'type' => 'value', 'name' => 'ctype'])[0];
    if (!$item) {
        $sql->setTable('rex_yform_field');
        $sql->setValue('prio', 3);
        $sql->setValue('table_name', 'rex_shop_customer_address');
        $sql->setValue('name', 'ctype');
        $sql->setValue('db_type', 'text');
        $sql->setValue('label', '###label.ctype###');
        $sql->setValue('type_id', 'value');
        $sql->setValue('type_name', 'choice');
        $sql->setValue('choices', '###label.person###=person,###label.company###=company');
        $sql->setValue('default', 'person');
        $sql->setValue('attributes', '{"onchange":"Simpleshop.changeCType(this)","data-init-form-toggle":"1"}');
        $sql->setValue('multiple', 0);
        $sql->setValue('list_hidden', 1);
        $sql->setValue('search', 0);
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

    $sql->setQuery('UPDATE rex_yform_table SET list_sortfield = "id", list_amount = 100, list_sortorder = "DESC" WHERE table_name = "rex_shop_customer"');

    $sql->setQuery('UPDATE rex_yform_field SET field = "id" WHERE `table` = "rex_shop_customer"');
    $sql->setQuery('UPDATE rex_yform_field SET no_db = 1 WHERE `table` = "rex_shop_customer" AND type_id = "value" AND `name` = "password"');
    $sql->setQuery('UPDATE rex_yform_field SET type_name = "choice", db_type = "text", options="", `default`="person", `table_name`="rex_shop_customer_address", choices="{\"###label.private_person###\":\"person\",\"###label.company###\":\"company\"}", attributes="{\"onchange\":\"Simpleshop.changeCType(this)\",\"data-init-form-toggle\":\"1\"}" WHERE table_name = "rex_shop_customer_address" AND type_id = "value" AND name = "ctype"');
    $sql->setQuery('UPDATE rex_yform_field SET `table_name`="rex_shop_customer_address" WHERE table_name = "rex_shop_customer" AND name = "fiscal_code"');
    $sql->setQuery('UPDATE rex_yform_field SET `table_name`="rex_shop_customer_address" WHERE table_name = "rex_shop_customer" AND name = "vat_num"');
    $sql->setQuery('UPDATE rex_yform_field SET list_hidden=1, attributes="{\"data-form-toggle\":\"person\"}" WHERE table_name = "rex_shop_customer_address" AND type_id = "value" AND name = "firstname"');
    $sql->setQuery('UPDATE rex_yform_field SET list_hidden=1, attributes="{\"data-form-toggle\":\"person\"}" WHERE table_name = "rex_shop_customer_address" AND type_id = "value" AND name = "lastname"');
    $sql->setQuery('UPDATE rex_yform_field SET list_hidden=1, attributes="{\"data-form-toggle\":\"company\"}" WHERE table_name = "rex_shop_customer_address" AND type_id = "value" AND name = "company_name"');
    $sql->setQuery('UPDATE rex_yform_field SET db_type="int" WHERE table_name = "rex_shop_customer_address" AND type_id = "value" AND name = "country"');
    $sql->setQuery('UPDATE rex_yform_field SET db_type="varchar(191)" WHERE table_name = "rex_shop_customer_address" AND type_id = "value" AND name = "fiscal_code"');
    $sql->setQuery('UPDATE rex_yform_field SET db_type="varchar(191)", attributes="{\"data-form-toggle\":\"person\"}" WHERE table_name = "rex_shop_customer_address" AND type_id = "value" AND name = "vat_num"');
    $sql->setQuery('UPDATE rex_yform_field SET type_name="empty", message="###error.field.empty###", validate_type=0 WHERE table_name = "rex_shop_customer_address" AND type_id = "validate" AND name = "firsname"');
    $sql->setQuery('UPDATE rex_yform_field SET type_name="empty", message="###error.field.empty###", validate_type=0 WHERE table_name = "rex_shop_customer_address" AND type_id = "validate" AND name = "lastname"');
    $sql->setQuery('UPDATE rex_yform_field SET type_name="customfunction", message="###error.field.empty###", `function`=:customvalidation, params="{\"method\":\"empty-if\",\"dependencies\":[{\"field\":\"ctype\",\"value\":\"company\"}]}", validate_type=0, attributes="{\"onchange\":\"Simpleshop.changeCType(this)\",\"data-init-form-toggle\":\"1\"}" WHERE table_name = "rex_shop_customer_address" AND type_id = "validate" AND name = "company_name"', ['customvalidation' => '\FriendsOfREDAXO\Simpleshop\Customer::customValidateField']);
    $sql->setQuery('UPDATE rex_yform_field SET type_name="customfunction", message="###error.field.fiscal_code_not_valid####", `function`=:customvalidation, params="{\"method\":\"fiscal_code\",\"dependencies\":[{\"field\":\"ctype\",\"value\":\"person\"},{\"field\":\"country\",\"value\":93,\"valueFrom\":\"post\"}]}", validate_type=0 WHERE table_name = "rex_shop_customer_address" AND type_id = "validate" AND name = "fiscal_code"', ['customvalidation' => '\FriendsOfREDAXO\Simpleshop\Customer::customValidateField']);
    $sql->setQuery('UPDATE rex_yform_field SET type_name="customfunction", message="###error.field.vat_not_valid####", `function`=:customvalidation, params="{\"method\":\"vat_num\",\"dependencies\":[{\"field\":\"ctype\",\"value\":\"company\"}]}", validate_type=0 WHERE table_name = "rex_shop_customer_address" AND type_id = "validate" AND name = "vat_num"', ['customvalidation' => '\FriendsOfREDAXO\Simpleshop\Customer::customValidateField']);


    $project = rex_addon::get('project');
    $neededFields = $project->getConfig('needed_yform_fields', []);
    $customerTable = rex_sql_table::get('rex_shop_customer');

    if (!in_array('rex_shop_customer.company_name', $neededFields)) {
        $sql->setQuery('DELETE FROM rex_yform_field WHERE table_name = "rex_shop_customer" AND name = "company_name"');
        $customerTable->removeColumn('company_name');
    }
    if (!in_array('rex_shop_customer.lastname', $neededFields)) {
        $sql->setQuery('DELETE FROM rex_yform_field WHERE table_name = "rex_shop_customer" AND name = "lastname"');
        $customerTable->removeColumn('lastname');
    }
    if (!in_array('rex_shop_customer.firstname', $neededFields)) {
        $sql->setQuery('DELETE FROM rex_yform_field WHERE table_name = "rex_shop_customer" AND name = "firstname"');
        $customerTable->removeColumn('firstname');
    }
    if (!in_array('rex_shop_customer.ctype', $neededFields)) {
        $sql->setQuery('DELETE FROM rex_yform_field WHERE table_name = "rex_shop_customer" AND name = "ctype"');
        $customerTable->removeColumn('ctype');
    }
    if (!in_array('rex_shop_customer.vat_num', $neededFields)) {
        $customerTable->removeColumn('vat_num');
    }
    if (!in_array('rex_shop_customer.fiscal_code', $neededFields)) {
        $customerTable->removeColumn('fiscal_code');
    }
    $customerTable->ensure();
}
finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}