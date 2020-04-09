<?php

$sql = rex_sql::factory();
$sql->setQuery('SET FOREIGN_KEY_CHECKS = 0');

try {
    rex_sql_table::get('rex_prj_country')
        ->ensureColumn(new rex_sql_column('id', 'int(11)', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('name_1', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_2', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_3', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_4', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('prio', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('status', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('iso2', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('iso3', 'text', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_category')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('name_1', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('text_1', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_2', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('text_2', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_3', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('text_3', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('parent_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('subcategories', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('status', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('prio', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_coupon')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('name_1', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_2', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_3', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_4', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('given_away', 'tinyint(1)', false, null, null))
        ->ensureColumn(new rex_sql_column('start_time', 'date', false, null, null))
        ->ensureColumn(new rex_sql_column('discount_value', 'decimal(10,2)', true, null, null))
        ->ensureColumn(new rex_sql_column('discount_percent', 'decimal(4,1)', true, null, null))
        ->ensureColumn(new rex_sql_column('prefix', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('code', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('orders', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_customer')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('lang_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('ctype', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('company_name', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('firstname', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('lastname', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('email', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('password', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('password_hash', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('fiscal_code', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('vat_num', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('lastlogin', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('status', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('created', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_customer_address')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('customer_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('company_name', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('firstname', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('lastname', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('street', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('street_additional', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('location', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('province', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('postal', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('country', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('status', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_discount_group')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('name_1', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_2', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_3', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_4', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('ctype', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('price', 'decimal(10,2)', true, null, null))
        ->ensureColumn(new rex_sql_column('amount', 'int(11)', true, null, null))
        ->ensureColumn(new rex_sql_column('discount_percent', 'int(11)', true, null, null))
        ->ensureColumn(new rex_sql_column('discount_value', 'decimal(10,2)', true, null, null))
        ->ensureColumn(new rex_sql_column('free_shipping', 'tinyint(1)', false, null, null))
        ->ensureColumn(new rex_sql_column('status', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('prio', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_feature')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('name_1', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_2', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_3', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_4', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('key', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('status', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('prio', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_feature_values')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('name_1', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_2', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_3', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_4', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('feature_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('key', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('status', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('prio', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_order')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('customer_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('invoice_num', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('ref_order_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('status', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('initial_total', 'decimal(10,2)', true, null, null))
        ->ensureColumn(new rex_sql_column('brut_prices', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('taxes', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('net_prices', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('shipping_costs', 'decimal(10,2)', true, null, null))
        ->ensureColumn(new rex_sql_column('discount', 'decimal(10,2)', true, null, null))
        ->ensureColumn(new rex_sql_column('manual_discount', 'decimal(10,2)', true, null, null))
        ->ensureColumn(new rex_sql_column('total', 'decimal(10,2)', true, null, null))
        ->ensureColumn(new rex_sql_column('quantity', 'int(11)', true, null, null))
        ->ensureColumn(new rex_sql_column('promotions', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('customer_data', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('invoice_address', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('shipping_address', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('payment', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('shipping', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('shipping_key', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('extras', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('ip_address', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('notes', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_order_products')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('order_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('product_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('code', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('cart_quantity', 'int(11)', true, null, null))
        ->ensureColumn(new rex_sql_column('data', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('shipping_key', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_package')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('name_1', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('description_1', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_2', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('description_2', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_3', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('description_3', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_4', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('description_4', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('products', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('code', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('status', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('prio', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_product')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('name_1', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('subtitle_1', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('short_description_1', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('description_1', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_2', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('subtitle_2', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('short_description_2', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('description_2', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_3', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('subtitle_3', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('short_description_3', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('description_3', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_4', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('subtitle_4', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('short_description_4', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('description_4', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('type', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('hashtag', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('video', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('code', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('category', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('optionals', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('category_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('images', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('inventory', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('amount', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('b2b_sale_amount', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('price', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('reduced_price', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('tax', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('length', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('width', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('height', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('weight', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('features', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('related_products', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('status', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('prio', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_product_has_category')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('product_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('category_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('prio', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_product_has_feature')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('type', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('name_1', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('material_1', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_2', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('material_2', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('name_3', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('material_3', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('product_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('variant_key', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('code', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('amount', 'int(11)', true, null, null))
        ->ensureColumn(new rex_sql_column('price', 'decimal(10,2)', true, null, null))
        ->ensureColumn(new rex_sql_column('images', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('material_image', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('prio', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_session')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('customer_id', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('session_id', 'varchar(191)', false, null, null))
        ->ensureColumn(new rex_sql_column('last_url', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('cart_items', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('last_cart_update', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('lastupdate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_shop_tax')
        ->ensureColumn(new rex_sql_column('id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('prio', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('status', 'int(11)', false, null, null))
        ->ensureColumn(new rex_sql_column('tax', 'int(11)', true, null, null))
        ->setPrimaryKey(['id'])
        ->ensure();

    rex_sql_table::get('rex_yform_field')
        ->ensureColumn(new rex_sql_column('scale', 'text', false, null, null), 'not_required')
        ->ensureColumn(new rex_sql_column('options', 'text', false, null, null), 'scale')
        ->ensureColumn(new rex_sql_column('default', 'text', false, null, null), 'options')
        ->ensureColumn(new rex_sql_column('no_db', 'text', false, null, null), 'default')
        ->ensureColumn(new rex_sql_column('type', 'text', false, null, null), 'no_db')
        ->ensureColumn(new rex_sql_column('message', 'text', false, null, null), 'type')
        ->ensureColumn(new rex_sql_column('empty_value', 'text', false, null, null), 'message')
        ->ensureColumn(new rex_sql_column('notice', 'text', false, null, null), 'empty_value')
        ->ensureColumn(new rex_sql_column('format', 'text', false, null, null), 'notice')
        ->ensureColumn(new rex_sql_column('only_empty', 'text', false, null, null), 'format')
        ->ensureColumn(new rex_sql_column('fields', 'text', false, null, null), 'only_empty')
        ->ensureColumn(new rex_sql_column('table', 'text', false, null, null), 'fields')
        ->ensureColumn(new rex_sql_column('field', 'text', false, null, null), 'table')
        ->ensureColumn(new rex_sql_column('empty_option', 'text', false, null, null), 'field')
        ->ensureColumn(new rex_sql_column('widget', 'text', false, null, null), 'empty_option')
        ->ensureColumn(new rex_sql_column('attributes', 'text', false, null, null), 'widget')
        ->ensureColumn(new rex_sql_column('preview', 'text', false, null, null), 'attributes')
        ->ensureColumn(new rex_sql_column('multiple', 'text', false, null, null), 'preview')
        ->ensureColumn(new rex_sql_column('values', 'text', false, null, null), 'multiple')
        ->ensureColumn(new rex_sql_column('relation_table', 'text', false, null, null), 'values')
        ->ensureColumn(new rex_sql_column('function', 'text', false, null, null), 'relation_table')
        ->ensureColumn(new rex_sql_column('salt', 'text', false, null, null), 'function')
        ->ensureColumn(new rex_sql_column('filter', 'text', false, null, null), 'salt')
        ->ensureColumn(new rex_sql_column('current_date', 'text', false, null, null), 'filter')
        ->ensureColumn(new rex_sql_column('html', 'text', false, null, null), 'current_date')
        ->ensureColumn(new rex_sql_column('show_value', 'text', false, null, null), 'html')
        ->ensureColumn(new rex_sql_column('params', 'text', false, null, null), 'show_value')
        ->ensureColumn(new rex_sql_column('rules', 'text', false, null, null), 'params')
        ->ensureColumn(new rex_sql_column('precision', 'text', false, null, null), 'rules')
        ->ensureColumn(new rex_sql_column('choices', 'text', false, null, null), 'precision')
        ->ensureColumn(new rex_sql_column('expanded', 'text', false, null, null), 'choices')
        ->ensureColumn(new rex_sql_column('unit', 'text', false, null, null), 'expanded')
        ->ensureColumn(new rex_sql_column('validate_type', 'text', false, null, null), 'unit')
        ->alter();


    $sql->setQuery(<<<'SQL'
        INSERT INTO `rex_yform_field` (`table_name`, `prio`, `type_id`, `type_name`, `db_type`, `list_hidden`, `search`, `name`, `label`, `not_required`, `scale`, `options`, `default`, `no_db`, `type`, `message`, `empty_value`, `notice`, `format`, `only_empty`, `fields`, `table`, `field`, `empty_option`, `widget`, `attributes`, `preview`, `multiple`, `values`, `relation_table`, `function`, `salt`, `filter`, `current_date`, `html`, `show_value`, `params`, `rules`, `precision`, `choices`, `expanded`, `unit`, `validate_type`)
        VALUES
            ('rex_shop_category', 1, 'value', 'tab_start', 'none', 0, 0, 'tab_start', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 2, 'value', 'text', 'varchar(191)', 0, 1, 'name_1', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 3, 'value', 'textarea', 'text', 1, 0, 'text_1', 'Beschreibung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 4, 'value', 'tab_break', 'none', 0, 0, 'tab_break_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 5, 'value', 'text', 'varchar(191)', 1, 1, 'name_2', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 6, 'value', 'textarea', 'text', 1, 0, 'text_2', 'Beschreibung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 7, 'value', 'tab_break', 'none', 0, 0, 'tab_break_2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 8, 'value', 'text', 'varchar(191)', 1, 0, 'name_3', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 9, 'value', 'textarea', 'text', 1, 0, 'text_3', 'Beschreibung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 10, 'value', 'tab_end', 'none', 0, 0, 'tab_end', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 11, 'value', 'hidden_input', 'text', 1, 0, 'parent_id', 'Übergeordnete Kategorie', '', '', '', '', 0, 2, '', '', '', '', '', '', 'rex_shop_category', 'name_1, \" [\",id,\"]\"', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 12, 'value', 'hidden_input', 'text', 1, 1, 'subcategories', 'Unterkategorien', '', '', '', '', 0, 4, '', '', '', '', '', '', 'rex_shop_category', 'parent_id', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 13, 'value', 'choice', 'int', 1, 0, 'status', 'Status', '', '', 'Aktiv=1,Inaktiv=0', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"Aktiv\":1,\"Inaktiv\":0}', 0, '', ''),
            ('rex_shop_category', 14, 'value', 'prio', 'int', 1, 0, 'prio', 'Priorität', '', '', '', 1, '', '', '', '', '', '', '', 'name_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_category', 15, 'value', 'datestamp', 'datetime', 0, 0, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_category', 16, 'value', 'datestamp', 'datetime', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_prj_country', 1, 'value', 'tab_start', 'none', 0, 0, 'tab_start', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 2, 'value', 'text', 'varchar(191)', 0, 1, 'name_1', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 3, 'validate', 'empty', '', 1, 0, 'name_1', '', '', '', '', '', '', '', 'Bitte geben Sie in jeder Sprache eine Bezeichnung ein', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 4, 'value', 'tab_break', 'none', 0, 0, 'tab_break_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 5, 'value', 'text', 'varchar(191)', 1, 0, 'name_2', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 6, 'validate', 'empty', '', 1, 0, 'name_2', '', '', '', '', '', '', '', 'Bitte geben Sie in jeder Sprache eine Bezeichnung ein', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 7, 'value', 'tab_break', 'none', 0, 0, 'tab_break_2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 8, 'value', 'text', 'varchar(191)', 1, 0, 'name_3', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 9, 'validate', 'empty', '', 1, 0, 'name_3', '', '', '', '', '', '', '', 'Bitte geben Sie in jeder Sprache eine Bezeichnung ein', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 10, 'value', 'tab_break', 'none', 0, 0, 'tab_break_3', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 11, 'value', 'text', 'varchar(191)', 1, 0, 'name_4', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 12, 'validate', 'empty', '', 1, 0, 'name_4', '', '', '', '', '', '', '', 'Bitte geben Sie in jeder Sprache eine Bezeichnung ein', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 13, 'value', 'tab_end', 'none', 0, 0, 'tab_end', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 14, 'value', 'text', 'varchar(191)', 1, 0, 'iso2', 'ISO2', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 15, 'value', 'text', 'varchar(191)', 1, 0, 'iso3', 'ISO3', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 16, 'value', 'select', 'text', 0, 0, 'b2b_has_tax', 'B2B-Kunde ist steuerpflichtig', '', '', 'Ja=1,Nein=0', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 17, 'value', 'prio', '', 1, 0, 'prio', 'Priorität', '', '', '', 1, '', '', '', '', '', '', '', 'name_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_prj_country', 18, 'value', 'choice', 'int', 0, 1, 'status', 'Status', '', '', 'Aktiv=1,Inaktiv=0', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"Aktiv\":\"1\", \"Inaktiv\":\"0\"}', 0, '', ''),
            ('rex_shop_coupon', 1, 'value', 'coupon_functions', 'none', 0, 0, 'coupon_functions', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 2, 'value', 'tab_start', 'none', 0, 0, 'tab_start', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 3, 'value', 'text', 'varchar(191)', 0, 0, 'name_1', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"placeholder\":\"10 € Gutschein\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 4, 'value', 'tab_break', 'none', 0, 0, 'tab_break_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 5, 'value', 'text', 'varchar(191)', 1, 0, 'name_2', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 6, 'value', 'tab_break', 'none', 0, 0, 'tab_break_2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 7, 'value', 'text', 'varchar(191)', 1, 0, 'name_3', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 8, 'value', 'tab_break', 'none', 0, 0, 'tab_break_3', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 9, 'value', 'text', 'varchar(191)', 1, 0, 'name_4', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 10, 'value', 'tab_end', 'none', 0, 0, 'tab_end', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 11, 'value', 'checkbox', 'tinyint(1)', 0, 1, 'given_away', 'ist vergeben', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0,1', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 12, 'value', 'date', '', 0, 0, 'start_time', 'Start', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'input:text', '{\"data-yform-tools-datepicker\":\"YYYY-MM-DD\"}', '', '', '', '', '', '', '', 1, '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 13, 'value', 'number', '', 1, 0, 'discount_value', 'Rabatt als festen Betrag', '', 2, '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 10, '', '', '€', ''),
            ('rex_shop_coupon', 14, 'value', 'number', '', 1, 0, 'discount_percent', '[ODER] Rabatt in Prozent', '', 1, '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 4, '', '', '%', ''),
            ('rex_shop_coupon', 15, 'value', 'text', 'varchar(191)', 1, 1, 'prefix', 'Prefix', '', '', '', '', 0, '', '', '', 'ist optional', '', '', '', '', '', '', '', '{\"placeholder\":\"AA\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 16, 'value', 'coupon_code', 'text', 0, 1, 'code', 'Code', '', '', '', '', '', '', '', '', 'wird automatisch beim Speichern generiert', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 17, 'value', 'textarea', 'text', 1, 0, 'orders', 'Bestellungsdaten', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 18, 'value', 'datestamp', 'datetime', 0, 0, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_coupon', 19, 'value', 'datestamp', 'datetime', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 1, 'value', 'be_manager_relation', 'text', 1, 0, 'customer_id', 'Kunden-Id', '', '', '', '', '', 0, '', 'Bitte die Adresse einem Kunden zuweisen', '', '', '', '', 'rex_shop_customer', 'firstname,\" \",lastname', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 2, 'value', 'text', 'varchar(191)', 0, 0, 'company_name', '###label.company_name###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 3, 'validate', 'empty', '', 1, 0, 'company_name', '', '', '', '', '', '', '', '###error.insert_company_name###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 4, 'value', 'text', 'varchar(191)', 0, 1, 'firstname', '###label.firstname###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 5, 'validate', 'empty', '', 1, 0, 'firstname', '', '', '', '', '', '', '', '###error.insert_firstname###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 6, 'value', 'text', 'varchar(191)', 0, 1, 'lastname', '###label.lastname###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 7, 'validate', 'empty', '', 1, 0, 'lastname', '', '', '', '', '', '', '', '###error.insert_lastname###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 8, 'value', 'text', 'varchar(191)', 1, 1, 'street', '###label.street###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 9, 'validate', 'empty', '', 1, 0, 'street', '', '', '', '', '', '', '', '###error.insert_street###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 10, 'value', 'text', 'varchar(191)', 1, 0, 'street_additional', '###label.address_additional###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 11, 'value', 'text', 'varchar(191)', 0, 1, 'location', '###label.location###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 12, 'validate', 'empty', '', 1, 0, 'location', '', '', '', '', '', '', '', '###error.insert_location###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 13, 'value', 'text', 'varchar(191)', 1, 0, 'province', '###label.province###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 14, 'value', 'text', 'varchar(191)', 1, 0, 'postal', '###label.postal###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 15, 'validate', 'empty', '', 1, 0, 'postal', '', '', '', '', '', '', '', '###error.insert_postal###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 16, 'value', 'be_manager_relation', 'text', 0, 0, 'country', '###label.country###', '', '', '', '', '', 0, '', 'Bitte ein Land auswählen', '', '', '', '', 'rex_prj_country', 'name_1', 0, '', '', '', '', '', '', '', '', 'status = 1', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 17, 'value', 'choice', 'int', 0, 0, 'status', 'Status', '', '', 'Aktiv=1,Nicht aktiv=0', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"Aktiv\":1,\"Inaktiv\":0}', 0, '', ''),
            ('rex_shop_customer_address', 18, 'value', 'datestamp', 'datetime', 0, 0, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_customer_address', 19, 'value', 'datestamp', 'datetime', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_customer', 1, 'value', 'choice', 'text', 1, 0, 'lang_id', 'Sprache', '', '', 'Deutsch=1,Italiano=2,English=3,Francais=4', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', 'SELECT id AS value, name AS label FROM rex_clang', 0, '', ''),
            ('rex_shop_customer', 2, 'value', 'hidden_input', 'text', 1, 0, 'ctype', '###label.customer_type###', '', '', '###label.private_person###=person,###label.company###=company', 'person', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 3, 'value', 'hidden_input', 'text', 0, 0, 'company_name', '###label.company_name###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 4, 'validate', 'customfunction', '', 1, 0, 'company_name', '', '', '', '', '', '', '', '###error.insert_company_name###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '\\FriendsOfREDAXO\\Simpleshop\\Customer::customValidateField', '', '', '', '', '', '{\"method\":\"empty-if\",\"dependencies\":[{\"field\":\"ctype\",\"value\":\"company\"}]}', '', '', '', '', '', 0),
            ('rex_shop_customer', 5, 'value', 'text', 'varchar(191)', 0, 1, 'firstname', '###label.firstname###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 6, 'validate', 'customfunction', '', 1, 0, 'firstname', '', '', '', '', '', '', '', '###error.insert_firstname###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '\\FriendsOfREDAXO\\Simpleshop\\Customer::customValidateField', '', '', '', '', '', '{\"method\":\"empty-if\",\"dependencies\":[{\"field\":\"ctype\",\"value\":\"person\"}]}', '', '', '', '', '', 0),
            ('rex_shop_customer', 7, 'value', 'text', 'varchar(191)', 0, 1, 'lastname', '###label.lastname###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 8, 'validate', 'customfunction', '', 1, 0, 'lastname', '', '', '', '', '', '', '', '###error.insert_lastname###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '\\FriendsOfREDAXO\\Simpleshop\\Customer::customValidateField', '', '', '', '', '', '{\"method\":\"empty-if\",\"dependencies\":[{\"field\":\"ctype\",\"value\":\"person\"}]}', '', '', '', '', '', 0),
            ('rex_shop_customer', 9, 'value', 'email', 'varchar(191)', 0, 1, 'email', '###label.email###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 10, 'validate', 'unique', '', 1, 0, 'email', '', '', '', '', '', '', '', '###error.email_already_exists###', '', '', '', '', '', 'rex_shop_customer', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 11, 'validate', 'email', '', 1, 0, 'email', '', '', '', '', '', '', '', '###error.email_not_valid###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 12, 'value', 'text', 'varchar(191)', 1, 0, 'password', '###label.password###', '', '', '', '', 0, '', '', '', '###label.keep_password_notice###', '', '', '', '', '', '', '', '{\"type\":\"password\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 13, 'validate', 'password_policy', '', 1, 0, 'password', '', '', '', '', '', '', '', '###error.password_policy###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"length\":{\"min\":8},\"letter\":{\"min\":1},\"digit\":{\"min\":1}}', '', '', '', '', ''),
            ('rex_shop_customer', 14, 'value', 'hashvalue', 'text', 1, 0, 'password_hash', 'Passwort Hash', '', '', '', '', 0, '', '', '', '', '', '', '', '', 'password', '', '', '', '', '', '', '', 'sha1', 'UYD7FFtMLdqr4ZujqwED', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 15, 'value', 'text', 'varchar(191)', 1, 0, 'fiscal_code', '###label.fiscal_code###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 16, 'validate', 'customfunction', '', 1, 0, 'fiscal_code', '', '', '', '', '', '', '', '###error.field.fiscal_code_not_valid###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '\\FriendsOfREDAXO\\Simpleshop\\Customer::customValidateField', '', '', '', '', '', '{\"method\":\"fiscal_code\",\"dependencies\":[{\"field\":\"ctype\",\"value\":\"person\"},{\"field\":\"country\",\"value\":93,\"valueFrom\":\"post\"}]}', '', '', '', '', '', 0),
            ('rex_shop_customer', 17, 'value', 'hidden_input', 'text', 1, 0, 'vat_num', '###label.vat_short###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 18, 'validate', 'customfunction', '', 1, 0, 'vat_num', '', '', '', '', '', '', '', '###error.field.vat_not_valid###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '\\FriendsOfREDAXO\\Simpleshop\\Customer::customValidateField', '', '', '', '', '', '{\"method\":\"vat_num\",\"dependencies\":[{\"field\":\"ctype\",\"value\":\"company\"}]}', '', '', '', '', '', 0),
            ('rex_shop_customer', 19, 'value', 'be_manager_relation', 'text', 0, 0, 'addresses', 'Adressen', '', '', '', '', '', 4, '', '', '', '', '', '', 'rex_shop_customer_address', 'customer_id', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 20, 'value', 'datetime', 'datetime', 0, 0, 'lastlogin', 'Letzter Login', '', '', '', '', 0, '', '', '', '', 'DD.MM.YYYY HH:ii', '', '', '', '', '', 'input:text', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', ''),
            ('rex_shop_customer', 21, 'value', 'choice', 'text', 1, 0, 'status', 'Status', '', '', 'Aktiv=1,Inaktiv=0', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"Aktiv\":1,\"Inaktiv\":0}', 0, '', ''),
            ('rex_shop_customer', 22, 'value', 'datestamp', 'datetime', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, '', '', '', '', '', '', ''),
            ('rex_shop_customer', 23, 'value', 'datestamp', 'datetime', 1, 0, 'created', 'Registriert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', 'input:text', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', 1, '', 1, '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 1, 'value', 'tab_start', 'none', 0, 0, 'tab_start', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 2, 'value', 'text', 'varchar(191)', 0, 1, 'name_1', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 3, 'value', 'tab_break', 'none', 0, 0, 'tab_break_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 4, 'value', 'text', 'varchar(191)', 1, 1, 'name_2', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 5, 'value', 'tab_break', 'none', 0, 0, 'tab_break_2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 6, 'value', 'text', 'varchar(191)', 1, 0, 'name_3', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 7, 'value', 'tab_break', 'none', 0, 0, 'tab_break_3', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 8, 'value', 'text', '', 1, 0, 'name_4', 'Bezeichnung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 9, 'value', 'tab_end', 'none', 0, 0, 'tab_end', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 10, 'value', 'fieldset', 'none', 0, 0, 'fs_1', 'Wenn folgendes erreicht wurde', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 11, 'value', 'choice', 'text', 0, 0, 'ctype', '###label.customer_type###', '', '', '###label.all###=all,###label.private_person###=person,###label.company###=company', 'all', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"###label.all###\":\"all\",\"###label.private_person###\":\"person\",\"###label.company###\":\"company\"}', 0, '', ''),
            ('rex_shop_discount_group', 12, 'value', 'number', '', 1, 1, 'price', 'Mindestens dieser Preis', '', 2, '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 10, '', '', '€', ''),
            ('rex_shop_discount_group', 13, 'value', 'integer', 'int', 1, 1, 'amount', '[oder] Mindestens diese Menge', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 14, 'value', 'fieldset', 'none', 0, 0, 'fs_2', 'Dann folgenden Rabatt geben', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 15, 'value', 'integer', 'int', 1, 1, 'discount_percent', 'Rabatt in Prozent', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '%', ''),
            ('rex_shop_discount_group', 16, 'value', 'number', '', 1, 1, 'discount_value', '[oder] Rabatt als festen Betrag ', '', 2, '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 10, '', '', '€', ''),
            ('rex_shop_discount_group', 17, 'value', 'html', 'none', 0, 0, 'free_shipping_label', 'Gratisversand', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '<label>[oder] Gratisversand</label>', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 18, 'value', 'checkbox', 'tinyint(1)', 1, 1, 'free_shipping', 'Versand ist kostenlos', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0,1', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 19, 'value', 'html', 'none', 0, 0, 'free_shipping_after', 'Gratisversand-After', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '<div style=\"margin-bottom:28px;\"></div>', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 20, 'value', 'choice', 'int', 1, 0, 'status', 'Status', '', '', 'Aktiv=1,Inaktiv=0', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"Aktiv\":1,\"Inaktiv\":0}', 0, '', ''),
            ('rex_shop_discount_group', 21, 'value', 'prio', 'int', 1, 0, 'prio', 'Priorität', '', '', '', 1, '', '', '', '', '', '', '', 'name_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 22, 'value', 'datestamp', 'datetime', 0, 0, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_discount_group', 23, 'value', 'datestamp', 'datetime', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_feature', 1, 'value', 'tab_start', 'none', 0, 0, 'tab_start', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 2, 'value', 'text', 'varchar(191)', 0, 0, 'name_1', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 3, 'value', 'tab_break', 'none', 0, 0, 'tab_break_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 4, 'value', 'text', 'varchar(191)', 1, 1, 'name_2', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 5, 'value', 'tab_break', 'none', 0, 0, 'tab_break_2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 6, 'value', 'text', 'varchar(191)', 1, 0, 'name_3', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 7, 'value', 'tab_break', 'none', 0, 0, 'tab_break_3', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 8, 'value', 'text', 'varchar(191)', 1, 0, 'name_4', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 9, 'value', 'tab_end', 'none', 0, 0, 'tab_end', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 10, 'value', 'text', 'varchar(191)', 0, 0, 'key', 'Schlüssel', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 11, 'value', 'be_manager_relation', 'text', 0, 1, 'values', 'Werte', '', '', '', '', '', 4, '', '', '', '', '', '', 'rex_shop_feature_values', 'feature_id', 1, '', '', '', '', '', 'rex_shop_feature_values', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 12, 'value', 'choice', 'int', 1, 0, 'status', 'Status', '', '', 'Aktiv=1,Inaktiv=0', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"Aktiv\":1,\"Inaktiv\":0}', 0, '', ''),
            ('rex_shop_feature', 13, 'value', 'prio', 'int', 1, 0, 'prio', 'Priorität', '', '', '', 1, '', '', '', '', '', '', '', 'name_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature', 14, 'value', 'datestamp', 'datetime', 0, 0, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_feature', 15, 'value', 'datestamp', 'datetime', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 1, 'value', 'tab_start', 'none', 0, 0, 'tab_start', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 2, 'value', 'text', 'varchar(191)', 0, 1, 'name_1', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 3, 'value', 'tab_break', 'none', 0, 0, 'tab_break_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 4, 'value', 'text', 'varchar(191)', 1, 1, 'name_2', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 5, 'value', 'tab_break', 'none', 0, 0, 'tab_break_2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 6, 'value', 'text', 'varchar(191)', 1, 0, 'name_3', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 7, 'value', 'tab_break', 'none', 0, 0, 'tab_break_3', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 8, 'value', 'text', 'varchar(191)', 1, 0, 'name_4', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 9, 'value', 'tab_end', 'none', 0, 0, 'tab_end', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 10, 'value', 'be_manager_relation', 'text', 0, 1, 'feature_id', 'Eigenschaft', '', '', '', '', '', 0, '', 'Bitte eine Eigenschaft auswählen', '', '', '', '', 'rex_shop_feature', 'name_1', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 11, 'value', 'text', 'varchar(191)', 0, 1, 'key', 'Schlüssel', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 12, 'value', 'choice', 'int', 1, 0, 'status', 'Status', '', '', 'Aktiv=1,Inaktiv=0', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"Aktiv\":1,\"Inaktiv\":0}', 0, '', ''),
            ('rex_shop_feature_values', 13, 'value', 'prio', 'int', 1, 0, 'prio', 'Priorität', '', '', '', 1, '', '', '', '', '', '', '', 'name_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 14, 'value', 'datestamp', 'datetime', 0, 0, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_feature_values', 15, 'value', 'datestamp', 'datetime', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_order', 1, 'value', 'order_functions', 'none', 0, 0, 'order_functions', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 2, 'value', 'be_manager_relation', 'text', 0, 1, 'customer_id', 'Kunde', '', '', '', '', '', 2, '', 'Bitte Kunde auswählen', '', '', '', '', 'rex_shop_customer', 'firstname,\" \",lastname', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 3, 'value', 'cst_shop_inputs', 'text', 0, 0, 'invoice_num', '###label.invoice_num###', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 4, 'value', 'hidden_input', 'text', 1, 0, 'ref_order_id', 'Gutschrift-Rechnungs-Referenz', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 5, 'value', 'order_status_select', 'text', 1, 1, 'status', '###label.status###', '', '', 'auf Zahlung wartend=OP,in Bearbeitung=IP,Fehlgeschlagen=FA,Versendet=SH,Storniert=CA,Abgeschlossen=CL,Gutschrift=CN', 'OP', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 6, 'value', 'order_products', 'none', 0, 0, 'products', 'Produkte', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 7, 'value', 'number', '', 1, 0, 'initial_total', 'Ausgangssumme', '', 2, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', 10, '', '', '€', ''),
            ('rex_shop_order', 8, 'value', 'model_data', 'text', 1, 0, 'brut_prices', 'Bruttopreise', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 9, 'value', 'model_data', 'text', 1, 0, 'taxes', 'Steuern', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 10, 'value', 'model_data', 'text', 1, 0, 'net_prices', 'Nettopreise', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 11, 'value', 'number', '', 1, 0, 'shipping_costs', '###label.shipping_costs###', '', 2, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', 10, '', '', '€', ''),
            ('rex_shop_order', 12, 'value', 'number', '', 1, 0, 'discount', 'Rabatt Summe', '', 2, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', 10, '', '', '€', ''),
            ('rex_shop_order', 13, 'value', 'number', '', 1, 0, 'manual_discount', 'Manueller Rabatt', '', 2, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 10, '', '', '€', ''),
            ('rex_shop_order', 14, 'value', 'number', '', 0, 0, 'total', '###label.total_sum###', '', 2, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', 10, '', '', '€', ''),
            ('rex_shop_order', 15, 'value', 'integer', 'int', 1, 0, 'quantity', '###label.amount###', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 16, 'value', 'model_data', 'text', 1, 1, 'promotions', '###label.promotions###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 17, 'value', 'model_data', 'text', 1, 0, 'customer_data', 'Kundendaten', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 18, 'value', 'model_data', 'text', 1, 1, 'invoice_address', '###label.invoice_address###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 19, 'value', 'model_data', 'text', 1, 1, 'shipping_address', '###label.shipping_address###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 20, 'value', 'model_data', 'text', 1, 1, 'payment', '###label.payment_method###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 21, 'value', 'model_data', 'text', 1, 1, 'shipping', '###label.shipping_method###', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 22, 'value', 'text', 'varchar(191)', 1, 1, 'shipping_key', 'Versandschlüssel', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 23, 'value', 'model_data', 'text', 1, 0, 'extras', 'Extras', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 24, 'value', 'text', 'varchar(191)', 1, 1, 'ip_address', 'IP Adresse', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 25, 'value', 'textarea', 'text', 1, 1, 'notes', 'Notizen', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order', 26, 'value', 'datestamp', 'datetime', 0, 1, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_order', 27, 'value', 'datestamp', 'datetime', 1, 1, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_order_products', 1, 'value', 'hidden_input', 'text', 1, 1, 'order_id', 'Bestellung', '', '', '', '', 0, 0, '', '', '', '', '', '', 'rex_shop_order', 'ip_address', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order_products', 2, 'value', 'be_manager_relation', 'text', 1, 1, 'product_id', 'Produkt', '', '', '', '', '', 2, '', '', '', '', '', '', 'rex_shop_product', '', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order_products', 3, 'value', 'text', 'varchar(191)', 0, 1, 'code', 'Produkt-Nr', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order_products', 4, 'value', 'integer', 'int', 0, 1, 'cart_quantity', 'bestellte Menge', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order_products', 5, 'value', 'model_data', 'text', 1, 1, 'data', 'Produkt-Daten', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order_products', 6, 'value', 'text', 'varchar(191)', 1, 1, 'shipping_key', 'Versandschlüssel', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_order_products', 7, 'value', 'datestamp', 'datetime', 0, 1, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_order_products', 8, 'value', 'datestamp', 'datetime', 1, 1, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_package', 1, 'value', 'tab_start', 'none', 0, 0, 'tab_start', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 2, 'value', 'text', 'varchar(191)', 0, 1, 'name_1', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 3, 'value', 'textarea', 'text', 1, 0, 'description_1', 'Beschreibung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 4, 'value', 'tab_break', 'none', 0, 0, 'tab_break_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 5, 'value', 'text', 'varchar(191)', 1, 0, 'name_2', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 6, 'value', 'textarea', 'text', 1, 0, 'description_2', 'Beschreibung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 7, 'value', 'tab_break', 'none', 0, 0, 'tab_break_2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 8, 'value', 'text', 'varchar(191)', 1, 0, 'name_3', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 9, 'value', 'textarea', 'text', 1, 0, 'description_3', 'Beschreibung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 10, 'value', 'tab_break', 'none', 0, 0, 'tab_break_3', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 11, 'value', 'text', 'varchar(191)', 1, 0, 'name_4', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 12, 'value', 'textarea', 'text', 1, 0, 'description_4', 'Beschreibung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 13, 'value', 'tab_end', 'none', 0, 0, 'tab_end', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 14, 'value', 'product_variant_select', 'text', 0, 0, 'products', 'Produkte', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"data-minimum-input-length\":2}', '', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 15, 'value', 'text', 'varchar(191)', 0, 1, 'code', 'Produkt-Nr', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 16, 'value', 'choice', 'int', 1, 1, 'status', 'Status', '', '', 'Aktiv=1,Inaktiv=0', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"Aktive\":1,\"Inaktiv\":0}', 0, '', ''),
            ('rex_shop_package', 17, 'value', 'prio', 'int', 1, 0, 'prio', 'Priorität', '', '', '', 1, '', '', '', '', '', '', '', 'name_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_package', 18, 'value', 'datestamp', 'datetime', 1, 0, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_package', 19, 'value', 'datestamp', 'datetime', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_product_has_category', 1, 'value', 'be_manager_relation', 'text', 0, 1, 'product_id', 'Produkt', '', '', '', '', '', 0, '', 'Bitte Produkt auswählen', '', '', '', '', 'rex_shop_product', 'name_1', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_category', 2, 'value', 'be_manager_relation', 'text', 0, 1, 'category_id', 'Kategorie', '', '', '', '', '', 0, '', 'Bitte Kategorie auswählen', '', '', '', '', 'rex_shop_category', 'name_1', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_category', 3, 'value', 'prio', 'int', 1, 0, 'prio', 'Priorität', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_category', 4, 'value', 'datestamp', 'datetime', 1, 0, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, '', '', '', '', '', '', ''),
            ('rex_shop_product_has_category', 5, 'value', 'datestamp', 'datetime', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 1, 'value', 'choice', 'text', 1, 0, 'type', 'Typ', '', '', 'Aktiv=A,Nicht Aktiv=NE', 'NE', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"Aktiv\":\"A\",\"Nicht Aktiv\":\"NE\"}', 0, '', ''),
            ('rex_shop_product_has_feature', 2, 'value', 'text', 'varchar(191)', 0, 1, 'name_1', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 3, 'value', 'text', 'varchar(191)', 1, 0, 'material_1', 'Materialbezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 4, 'value', 'text', 'varchar(191)', 0, 1, 'name_2', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 5, 'value', 'text', 'varchar(191)', 1, 0, 'material_2', 'Materialbezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 6, 'value', 'text', 'varchar(191)', 0, 1, 'name_3', 'Bezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 7, 'value', 'text', 'varchar(191)', 0, 0, 'material_3', 'Materialbezeichnung', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 8, 'value', 'be_manager_relation', 'text', 1, 1, 'product_id', 'Produkt', '', '', '', '', '', 0, '', '', '', '', '', '', 'rex_shop_product', 'name_1', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 9, 'value', 'text', 'varchar(191)', 0, 1, 'variant_key', 'Varianten-Schlüssel', '', '', '', '', 0, '', '', '', 'Beinhaltet alle Feature-IDs', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 10, 'value', 'text', 'varchar(191)', 0, 1, 'code', 'Produkt-Nr', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 11, 'value', 'integer', 'int', 0, 1, 'amount', 'Restmenge', '', 0, '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 6, '', '', '', ''),
            ('rex_shop_product_has_feature', 12, 'value', 'number', '', 0, 0, 'price', 'Preis', '', 2, '', ' ', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 10, '', '', '€', ''),
            ('rex_shop_product_has_feature', 13, 'value', 'be_media', 'text', 1, 0, 'images', 'Bild', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 14, 'value', 'be_media', 'text', 1, 0, 'material_image', 'Material-Bild', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 15, 'value', 'text', 'varchar(191)', 1, 0, 'prio', 'Priorität', '', '', '', '', 0, '', '', '', '', '', '', 'amount', '', '', '', '', '{\"class\":\"prio\",\"type\":\"hidden\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 16, 'value', 'datestamp', 'datetime', 0, 0, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_product_has_feature', 17, 'value', 'datestamp', 'datetime', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_product', 1, 'value', 'tab_start', '', 0, 0, 'tab_start', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 2, 'value', 'text', '', 0, 1, 'name_1', 'Bezeichnung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 3, 'value', 'text', '', 1, 0, 'subtitle_1', 'Untertitel', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 4, 'value', 'textarea', '', 1, 0, 'short_description_1', 'Kurzbeschreibung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 5, 'value', 'textarea', '', 1, 0, 'description_1', 'Beschreibung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 6, 'value', 'tab_break', '', 0, 0, 'tab_break_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 7, 'value', 'text', '', 1, 0, 'name_2', 'Bezeichnung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 8, 'value', 'text', '', 1, 0, 'subtitle_2', 'Untertitel', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 9, 'value', 'textarea', '', 1, 0, 'short_description_2', 'Kurzbeschreibung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 10, 'value', 'textarea', '', 1, 0, 'description_2', 'Beschreibung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 11, 'value', 'tab_break', '', 0, 0, 'tab_break_2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 12, 'value', 'text', '', 1, 0, 'name_3', 'Bezeichnung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 13, 'value', 'text', '', 1, 0, 'subtitle_3', 'Untertitel', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 14, 'value', 'textarea', '', 1, 0, 'short_description_3', 'Kurzbeschreibung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 15, 'value', 'textarea', '', 1, 0, 'description_3', 'Beschreibung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 16, 'value', 'tab_break', '', 0, 0, 'tab_break_3', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 17, 'value', 'text', '', 1, 0, 'name_4', 'Bezeichnung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 18, 'value', 'text', '', 1, 0, 'subtitle_4', 'Untertitel', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 19, 'value', 'textarea', '', 1, 0, 'short_description_4', 'Kurzbeschreibung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 20, 'value', 'textarea', '', 1, 0, 'description_4', 'Beschreibung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"tinyMCEEditor\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 21, 'value', 'tab_end', '', 0, 0, 'tab_end', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 22, 'value', 'hidden_input', '', 1, 1, 'type', 'Produkttyp', '', '', 'Produkt=product,Produkt auf Anfrage=product_request,Geschenkgutschein=giftcard', 'product', '', '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"form-control product-type\"}', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 23, 'value', 'text', '', 0, 1, 'hashtag', 'Hashtag', '', '', '', '', '', '', '', '', 'Ohne \"#\"', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 24, 'value', 'text', '', 1, 0, 'video', 'Youtube Video', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"placeholder\":\"https://www.youtube.com/watch?v=Ai9f7cRzCIY\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 25, 'value', 'text', '', 0, 1, 'code', 'Produkt-Nr', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 26, 'value', 'be_link', '', 0, 1, 'category', 'Kategorie', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 27, 'validate', 'empty', '', 1, 0, 'category', '', '', '', '', '', '', '', 'Bitte geben Sie eine Produkt-Kategorie an', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 28, 'value', 'select', '', 1, 0, 'optionals', 'Optionen', '', '', 'biologisch=bio', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"class\":\"form-control select2\"}', '', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 29, 'value', 'hidden_input', '', 1, 0, 'category_id', 'Kategorie', '', '', '', '', '', 2, '', 'Bitte eine Kategorie auswählen', '', '', '', '', 'rex_shop_category', 'name_1', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 30, 'value', 'be_media', '', 1, 0, 'images', 'Bilder', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 31, 'value', 'hidden_input', '', 1, 0, 'inventory', 'Inventar', '', '', 'nicht verfolgen=N,verfolgen=F', 'N', '', '', '', '', 'wenn Inventar nicht verfolgt wird muss man bei Menge 1 (= verfügbar) oder 0 (= nicht verfügbar) eintragen', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 32, 'value', 'text', '', 1, 0, 'amount', 'Maximale Bestellmenge pro Warenkorb', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 33, 'validate', 'type', '', 1, 0, 'amount', '', 1, '', '', '', '', 'int', 'amount must be an integer', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 34, 'value', 'text', '', 0, 0, 'b2b_sale_amount', 'Anzahl Produkte in B2B-Kommission', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"placeholder\":6}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 35, 'validate', 'type', '', 1, 0, 'b2b_sale_amount', '', 0, '', '', '', '', 'int', 'Die Anzahl an Produkte in Kommission muss eine Ganzzahl > 1 sein', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 36, 'value', 'float', '', 0, 0, 'price', 'Preis', '', 4, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 37, 'value', 'float', '', 1, 0, 'reduced_price', 'Reduzierter Preis', '', 2, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 38, 'value', 'be_manager_relation', '', 1, 0, 'tax', 'Steuersatz', '', '', '', '', '', 0, '', '', '', '', '', '', 'rex_shop_tax', 'tax', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 39, 'value', 'hidden_input', '', 1, 0, 'length', 'Länge in mm', '', 2, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 40, 'value', 'hidden_input', '', 1, 0, 'width', 'Breite in mm', '', 2, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 41, 'value', 'hidden_input', '', 1, 0, 'height', 'Höhe in mm', '', 2, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 42, 'value', 'hidden_input', '', 1, 0, 'weight', 'Gewicht in g', '', 2, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 43, 'value', 'be_manager_relation', '', 1, 0, 'features', 'Eigenschaften', '', '', '', '', '', 3, '', '', '', '', '', '', 'rex_shop_feature_values', 'name_1', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 44, 'value', 'product_variant_select', '', 1, 1, 'related_products', 'zugehörige Produkte', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"data-minimum-input-length\":2}', '', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 45, 'value', 'select', '', 1, 0, 'status', 'Status', '', '', 'Aktiv=1,Inaktiv=0', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 46, 'value', 'prio', '', 1, 0, 'prio', 'Priorität', '', '', '', '', '', '', '', '', '', '', '', 'name_1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 47, 'value', 'datestamp', '', 1, 0, 'createdate', 'Erstellt am', '', '', '', '', '', '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_product', 48, 'value', 'datestamp', '', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', '', '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_session', 1, 'value', 'be_manager_relation', '', 0, 1, 'customer_id', 'Kunden-Id', '', '', '', '', '', 2, '', '', '', '', '', '', 'rex_shop_customer', 'firstname,\" \",lastname', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_session', 2, 'value', 'text', '', 1, 0, 'session_id', 'Session-Id', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_session', 3, 'value', 'text', 'text', 0, 0, 'last_url', 'letzte Url', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_session', 4, 'value', 'text', 'text', 1, 0, 'cart_items', 'Warenkorb', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_session', 5, 'value', 'datetime', '', 0, 0, 'last_cart_update', 'Letzte Warenkorb Änderung', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'input:text', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', ''),
            ('rex_shop_session', 6, 'value', 'datestamp', '', 0, 0, 'createdate', 'Erstellt am', '', '', '', '', '', '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_session', 7, 'value', 'datestamp', 'datetime', 0, 1, 'lastupdate', 'zuletzt geändert', '', '', '', '', 0, '', '', '', '', '', 0, '', '', '', '', 'input:text', '{\"readonly\":\"readonly\"}', '', '', '', '', '', '', '', 0, '', 1, '', '', '', '', '', '', ''),
            ('rex_shop_tax', 1, 'value', 'datestamp', 'datetime', 1, 0, 'updatedate', 'Aktualisiert am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_tax', 2, 'value', 'datestamp', 'datetime', 0, 0, 'createdate', 'Erstellt am', '', '', '', '', 0, '', '', '', '', 'Y-m-d H:i:s', 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', ''),
            ('rex_shop_tax', 3, 'value', 'prio', 'int', 1, 0, 'prio', 'Priorität', '', '', '', 1, '', '', '', '', '', '', '', 'tax', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            ('rex_shop_tax', 4, 'value', 'choice', 'int', 1, 0, 'status', 'Status', '', '', 'Aktiv=1,Inaktiv=0', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '{\"Aktiv\":1,\"Nicht aktiv\":0}', 0, '', ''),
            ('rex_shop_tax', 5, 'value', 'integer', 'int', 0, 0, 'tax', 'Steuersatz', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '%', '')
        ON DUPLICATE KEY UPDATE `table_name` = VALUES(`table_name`), `prio` = VALUES(`prio`), `type_id` = VALUES(`type_id`), `type_name` = VALUES(`type_name`), `db_type` = VALUES(`db_type`), `list_hidden` = VALUES(`list_hidden`), `search` = VALUES(`search`), `name` = VALUES(`name`), `label` = VALUES(`label`), `not_required` = VALUES(`not_required`), `scale` = VALUES(`scale`), `options` = VALUES(`options`), `default` = VALUES(`default`), `no_db` = VALUES(`no_db`), `type` = VALUES(`type`), `message` = VALUES(`message`), `empty_value` = VALUES(`empty_value`), `notice` = VALUES(`notice`), `format` = VALUES(`format`), `only_empty` = VALUES(`only_empty`), `fields` = VALUES(`fields`), `table` = VALUES(`table`), `field` = VALUES(`field`), `empty_option` = VALUES(`empty_option`), `widget` = VALUES(`widget`), `attributes` = VALUES(`attributes`), `preview` = VALUES(`preview`), `multiple` = VALUES(`multiple`), `values` = VALUES(`values`), `relation_table` = VALUES(`relation_table`), `function` = VALUES(`function`), `salt` = VALUES(`salt`), `filter` = VALUES(`filter`), `current_date` = VALUES(`current_date`), `html` = VALUES(`html`), `show_value` = VALUES(`show_value`), `params` = VALUES(`params`), `rules` = VALUES(`rules`), `precision` = VALUES(`precision`), `choices` = VALUES(`choices`), `expanded` = VALUES(`expanded`), `unit` = VALUES(`unit`), `validate_type` = VALUES(`validate_type`)
SQL
    );

    $sql->setQuery(<<<'SQL'
        INSERT INTO `rex_yform_table` (`status`, `table_name`, `name`, `description`, `list_amount`, `list_sortfield`, `list_sortorder`, `prio`, `search`, `hidden`, `add_new`, `export`, `import`, `mass_deletion`, `mass_edit`, `schema_overwrite`, `history`)
        VALUES
            (1, 'rex_shop_category', 'Kategorien', '', 50, 'prio', 'ASC', 7, 0, 1, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_prj_country', 'Länder', '', 255, 'prio', 'ASC', 15, 0, 0, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_coupon', 'Coupons', '', 50, 'id', 'DESC', 11, 1, 1, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_customer_address', 'Kunden-Adressen', '', 50, 'id', 'ASC', 10, 0, 1, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_customer', 'Kunden', '', 50, 'lastname', 'ASC', 9, 0, 0, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_discount_group', 'Rabattgruppen', '', 50, 'prio', 'ASC', 12, 0, 1, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_feature', 'Produkt-Eigenschaften', '', 255, 'prio', 'ASC', 4, 0, 0, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_feature_values', 'Eigenschafts-Werte', '', 255, 'prio', 'ASC', 5, 0, 1, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_order', 'Bestellungen', '', 50, 'id', 'DESC', 17, 1, 0, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_order_products', 'Bestellung-Hat-Produkte', '', 50, 'id', 'ASC', 16, 0, 1, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_package', 'Pakete', '', 100, 'name_1', 'ASC', 1, 1, 1, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_product_has_category', 'Produkt-Hat-Kategorie', '', 100000, 'prio', 'ASC', 23, 0, 1, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_product_has_feature', 'Varianten', '', 50, 'id', 'ASC', 6, 0, 1, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_product', 'Produkte', '', 255, 'prio', 'DESC', 3, 1, 0, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_session', 'Sessionen', '', 50, 'id', 'ASC', 13, 0, 1, 1, 0, 0, 0, 0, 1, 0),
            (1, 'rex_shop_tax', 'Steuersätze', '', 255, 'prio', 'ASC', 8, 0, 0, 1, 0, 0, 0, 0, 1, 0)
        ON DUPLICATE KEY UPDATE `status` = VALUES(`status`), `table_name` = VALUES(`table_name`), `name` = VALUES(`name`), `description` = VALUES(`description`), `list_amount` = VALUES(`list_amount`), `list_sortfield` = VALUES(`list_sortfield`), `list_sortorder` = VALUES(`list_sortorder`), `prio` = VALUES(`prio`), `search` = VALUES(`search`), `hidden` = VALUES(`hidden`), `add_new` = VALUES(`add_new`), `export` = VALUES(`export`), `import` = VALUES(`import`), `mass_deletion` = VALUES(`mass_deletion`), `mass_edit` = VALUES(`mass_edit`), `schema_overwrite` = VALUES(`schema_overwrite`), `history` = VALUES(`history`)
SQL
    );
} finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}
