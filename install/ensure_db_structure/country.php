<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 29.06.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$sql   = rex_sql::factory();
$table = \FriendsOfREDAXO\Simpleshop\Country::TABLE;

\Kreatif\Yform::ensureValueField($table, 'b2b_has_tax', [], [
    'type_name'   => 'select',
    'list_hidden' => 0,
    'search'      => 0,
    'label'       => 'translate:label.b2b_tax_required',
    'db_type'     => 'int',
    'options'     => 'translate:yes=1,translate:no=0',
    'default'     => 1,
    'prio'        => 10,
]);

$yTable = rex_yform_manager_table::get($table);
rex_yform_manager_table_api::generateTableAndFields($yTable);

