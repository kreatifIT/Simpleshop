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
    rex_sql_table::get('rex_shop_product')
        ->ensureColumn(new rex_sql_column('b2b_sale_amount', 'int(11)', true))
        ->ensureColumn(new rex_sql_column('price', 'decimal(11,4)', true))
        ->ensureColumn(new rex_sql_column('reduced_price', 'decimal(11,4)', true))
        ->ensureColumn(new rex_sql_column('tax', 'int(11)', true))
        ->ensureColumn(new rex_sql_column('features', 'text', true))
        ->ensureColumn(new rex_sql_column('status', 'int(11)', false))
        ->ensureColumn(new rex_sql_column('length', 'int(11) unsigned', true))
        ->ensureColumn(new rex_sql_column('width', 'int(11) unsigned', true))
        ->ensureColumn(new rex_sql_column('height', 'int(11) unsigned', true))
        ->ensureColumn(new rex_sql_column('weight', 'int(11) unsigned', true))
        ->ensureColumn(new rex_sql_column('prio', 'int(11) unsigned', true))
        ->removeColumn('category')
        ->alter();

    rex_sql_table::get('rex_shop_feature')
        ->ensureColumn(new rex_sql_column('prio', 'int(11) unsigned', true))
        ->alter();

    rex_sql_table::get('rex_shop_feature_values')
        ->ensureColumn(new rex_sql_column('feature_id', 'int(11)', false))
        ->ensureColumn(new rex_sql_column('prio', 'int(11) unsigned', true))
        ->alter();

    rex_sql_table::get('rex_shop_discount_group')
        ->ensureColumn(new rex_sql_column('prio', 'int(11) unsigned', true))
        ->alter();

    rex_sql_table::get('rex_shop_customer')
        ->ensureColumn(new rex_sql_column('lang_id', 'int(11)', true))
        ->ensureColumn(new rex_sql_column('status', 'int(11)', false))
        ->alter();

    rex_sql_table::get('rex_shop_customer_address')
        ->ensureColumn(new rex_sql_column('customer_id', 'int(11)', false))
        ->ensureColumn(new rex_sql_column('status', 'int(11)', false))
        ->ensureColumn(new rex_sql_column('vat_num', 'varchar(191)', true))
        ->alter();

    rex_sql_table::get('rex_shop_order')
        ->ensureColumn(new rex_sql_column('customer_id', 'int(11)', false))
        ->alter();

    rex_sql_table::get('rex_shop_order_products')
        ->ensureColumn(new rex_sql_column('order_id', 'int(11)', false))
        ->ensureColumn(new rex_sql_column('product_id', 'int(11)', false))
        ->alter();

    rex_sql_table::get('rex_shop_product_has_feature')
        ->ensureColumn(new rex_sql_column('product_id', 'int(11)', false))
        ->ensureColumn(new rex_sql_column('prio', 'int(11) unsigned', true))
        ->alter();

    rex_sql_table::get('rex_shop_session')
        ->ensureColumn(new rex_sql_column('customer_id', 'int(11)', false))
        ->alter();


    $item = $sql->getArray('SELECT id FROM rex_yform_field WHERE table_name = :table AND type_id = :type AND name = :name', ['table' => 'rex_shop_customer_address', 'type' => 'value', 'name' => 'ctype'])[0];
    if (!$item) {
        $sql->setTable('rex_yform_field');
        $sql->setValue('table_name', 'rex_shop_customer_address');
        $sql->setValue('prio', 1);
        $sql->setValue('type_id', 'value');
        $sql->setValue('type_name', 'choice');
        $sql->setValue('db_type', 'text');
        $sql->setValue('choices', '###label.person###=person,###label.company###=company');
        $sql->setValue('default', 'person');
        $sql->setValue('list_hidden', 0);
        $sql->setValue('search', 0);
        $sql->setValue('name', 'ctype');
        $sql->setValue('label', '###label.customer_type###');
        $sql->insert();
    }

    $sql->setQuery('
        UPDATE rex_yform_table SET hidden=1 WHERE table_name = "rex_shop_feature";
        
        DELETE FROM rex_yform_field WHERE table_name = "rex_shop_product" AND name = "category";
        
        UPDATE rex_yform_field SET type_name="data_output" WHERE table_name = "rex_shop_coupon" AND type_id = "value" AND name = "orders";
        
        UPDATE rex_yform_field SET db_type="int" WHERE table_name = "rex_shop_feature_values" AND type_id = "value" AND name = "feature_id";
        
        UPDATE rex_yform_field SET db_type="int" WHERE table_name = "rex_shop_customer" AND type_id = "value" AND name = "lang_id";
        UPDATE rex_yform_field SET db_type="int" WHERE table_name = "rex_shop_customer" AND type_id = "value" AND name = "status";
        
        UPDATE rex_yform_field SET db_type="int" WHERE table_name = "rex_shop_customer_address" AND type_id = "value" AND name = "customer_id";
        UPDATE rex_yform_field SET db_type="varchar(191)" WHERE table_name = "rex_shop_customer_address" AND type_id = "value" AND name = "vat_num";
        
        UPDATE rex_yform_field SET list_hidden=1, search=0, type_name="integer", db_type="int" WHERE table_name = "rex_shop_product" AND type_id = "value" AND name = "b2b_sale_amount";
        UPDATE rex_yform_field SET list_hidden=1, search=0, type_name="number", db_type="", `scale`=4, `precision`=11, unit="€" WHERE table_name = "rex_shop_product" AND type_id = "value" AND name = "price";
        UPDATE rex_yform_field SET list_hidden=1, search=0, type_name="number", db_type="", `scale`=4, `precision`=11, unit="€" WHERE table_name = "rex_shop_product" AND type_id = "value" AND name = "reduced_price";
        UPDATE rex_yform_field SET list_hidden=1, search=0, type_name="choice", db_type="int", choices="Aktiv=1,Inaktiv=0" WHERE table_name = "rex_shop_product" AND type_id = "value" AND name = "status";
        
        UPDATE rex_yform_field SET list_hidden=1, search=0, db_type="int" WHERE table_name = "rex_shop_tax" AND type_id = "value" AND name = "tax";
        
        UPDATE rex_yform_field SET db_type="int" WHERE table_name = "rex_shop_order" AND type_id = "value" AND name = "customer_id";
        UPDATE rex_yform_field SET type_name="data_output" WHERE table_name = "rex_shop_order" AND type_id = "value" AND name = "brut_prices";
        UPDATE rex_yform_field SET type_name="data_output" WHERE table_name = "rex_shop_order" AND type_id = "value" AND name = "taxes";
        UPDATE rex_yform_field SET type_name="data_output" WHERE table_name = "rex_shop_order" AND type_id = "value" AND name = "net_prices";
        
        UPDATE rex_yform_field SET type_name="be_manager_relation", db_type="int", list_hidden=0, search=1, `type`=2, empty_value="Es muss eine Bestellung zugewiesen werden", `table`="rex_shop_order", field="id", empty_option=0 WHERE table_name = "rex_shop_order_products" AND type_id = "value" AND name = "order_id";
        UPDATE rex_yform_field SET db_type="int", list_hidden=0, search=1 WHERE table_name = "rex_shop_order_products" AND type_id = "value" AND name = "product_id";
        
        UPDATE rex_yform_field SET db_type="int" WHERE table_name = "rex_shop_product_has_feature" AND type_id = "value" AND name = "product_id";
        
        UPDATE rex_yform_field SET db_type="int" WHERE table_name = "rex_shop_session" AND type_id = "value" AND name = "customer_id";
        UPDATE rex_yform_field SET type_name="data_output" WHERE table_name = "rex_shop_session" AND type_id = "value" AND name = "cart_items";
    ', []);
}
finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}