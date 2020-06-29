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
    $table = \FriendsOfREDAXO\Simpleshop\Order::TABLE;
    \Kreatif\Yform::ensureValueField($table, 'shipping_address_id', [], [
        'label' => '###label.address###',
    ]);
    $yTable = rex_yform_manager_table::get($table);
    rex_yform_manager_table_api::generateTableAndFields($yTable);
}
finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}