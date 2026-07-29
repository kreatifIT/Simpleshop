<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FriendsOfREDAXO\Simpleshop\Order;
use Kreatif\Yform;

$table = Order::TABLE;
$sql   = \rex_sql::factory();

$maxPrio = (int) $sql->getArray(
    'SELECT MAX(prio) AS max_prio FROM rex_yform_field WHERE table_name = :table',
    ['table' => $table]
)[0]['max_prio'];

Yform::ensureValueField(
    $table,
    'shipping_api_response',
    'data_output',
    [
        'list_hidden' => 1,
        'search'      => 0,
        'label'       => 'DFI Shipping API Response',
    ],
    [
        'prio'    => $maxPrio + 1,
        'db_type' => 'text',
    ]
);

$yTable = \rex_yform_manager_table::get($table);
\rex_yform_manager_table_api::generateTableAndFields($yTable);
