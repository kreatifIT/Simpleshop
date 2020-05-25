<?php

$sql = rex_sql::factory();
$sql->setQuery('SET FOREIGN_KEY_CHECKS = 0');

try {

    rex_sql_table::get(\FriendsOfREDAXO\Simpleshop\Order::TABLE)
        ->ensureColumn(new rex_sql_column('shipping_address_id', 'int(11)', true, null, null))
        ->ensure();

    $fieldId = $sql->getArray('SELECT id FROM rex_yform_field WHERE table_name = :table AND name = :field AND type_id = :type', [
        'table' => \FriendsOfREDAXO\Simpleshop\Order::TABLE,
        'field' => 'shipping_address_id',
        'type'  => 'value',
    ], PDO::FETCH_COLUMN)[0];

    $sql->setTable('rex_yform_field');
    $sql->setValue('table_name', \FriendsOfREDAXO\Simpleshop\Order::TABLE);
    $sql->setValue('prio', 3);
    $sql->setValue('type_id', 'value');
    $sql->setValue('type_name', 'customer_address');
    $sql->setValue('db_type', 'int');
    $sql->setValue('list_hidden', 0);
    $sql->setValue('search', 0);
    $sql->setValue('name', 'shipping_address_id');
    $sql->setValue('label', '###label.shipping_address###');
    $sql->setValue('empty_value', '###label.shipping_address###');
    $sql->setValue('empty_option', 0);

    if ($fieldId) {
        $sql->setWhere(['id' => $fieldId]);
        $sql->update();
    } else {
        $sql->insert();
    }
}
finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}
