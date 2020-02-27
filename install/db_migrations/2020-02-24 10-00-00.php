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
    rex_sql_table::get('rex_shop_product_has_feature')
        ->ensureColumn(new rex_sql_column('prio', 'int(11)'))
        ->ensure();

    rex_sql_table::get('rex_shop_product')
        ->ensureColumn(new rex_sql_column('amount', 'int(11)', false, 0))
        ->ensureColumn(new rex_sql_column('price', 'decimal(10,2)', false, 0))
        ->ensureColumn(new rex_sql_column('reduced_price', 'decimal(10,2)', false, 0))
        ->ensure();

    $sql->setQuery('
        UPDATE rex_yform_field SET type_name = "integer", db_type = "int" WHERE table_name = "rex_shop_product_has_feature" AND type_id = "value" AND name = "prio";
        UPDATE rex_yform_field SET type_name = "integer", db_type = "int" WHERE table_name = "rex_shop_product" AND type_id = "value" AND name = "amount";
        UPDATE rex_yform_field SET type_name = "number", db_type = "", scale="2", precision="10" WHERE table_name = "rex_shop_product" AND type_id = "value" AND name = "price";
        UPDATE rex_yform_field SET type_name = "number", db_type = "", scale="2", precision="10" WHERE table_name = "rex_shop_product" AND type_id = "value" AND name = "reduced_price";
    ');
}
finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}