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

$prio = $maxPrio + 1;

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
        'prio'    => $prio++,
        'db_type' => 'text',
    ]
);

Yform::ensureValueField(
    $table,
    'dfi_addorder_sent_at',
    'datestamp',
    [
        'list_hidden' => 1,
        'search'      => 0,
        'label'       => 'DFI Addorder übertragen am',
    ],
    [
        'prio'       => $prio++,
        'db_type'    => 'datetime',
        'format'     => 'Y-m-d H:i:s',
        'only_empty' => 2,
        'show_value' => 1,
    ]
);

Yform::ensureValueField(
    $table,
    'dfi_addorder_response',
    'data_output',
    [
        'list_hidden' => 1,
        'search'      => 0,
        'label'       => 'DFI Addorder API Response',
    ],
    [
        'prio'    => $prio++,
        'db_type' => 'text',
    ]
);

Yform::ensureValueField(
    $table,
    'dfi_preorder_sent_at',
    'datestamp',
    [
        'list_hidden' => 1,
        'search'      => 0,
        'label'       => 'DFI Pre-Ordine übertragen am',
    ],
    [
        'prio'       => $prio++,
        'db_type'    => 'datetime',
        'format'     => 'Y-m-d H:i:s',
        'only_empty' => 2,
        'show_value' => 1,
    ]
);

Yform::ensureValueField(
    $table,
    'dfi_preorder_response',
    'data_output',
    [
        'list_hidden' => 1,
        'search'      => 0,
        'label'       => 'DFI Pre-Ordine API Response',
    ],
    [
        'prio'    => $prio++,
        'db_type' => 'text',
    ]
);

$yTable = \rex_yform_manager_table::get($table);
\rex_yform_manager_table_api::generateTableAndFields($yTable);
