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

use Kreatif\Model\Country;
use Kreatif\Yform;

$table = Country::TABLE;

Yform::ensureValueField(
    $table,
    'vat_rate',
    'number',
    [],
    [
        'list_hidden' => 0,
        'search'      => 0,
        'label'       => 'MwSt.-Satz (%)',
        'precision'   => 5,
        'scale'       => 2,
        'default'     => '22',
        'db_type'     => 'decimal(5,2)',
        'prio'        => 11,
    ]
);

$yTable = \rex_yform_manager_table::get($table);
\rex_yform_manager_table_api::generateTableAndFields($yTable);

// One-time seed for newly created rows only (WHERE vat_rate IS NULL) - never
// overwrites a value that's already set, so a later reinstall (e.g. for an
// unrelated fix) can't clobber rates an admin has since adjusted in the
// backend country list.
//
// EU-27 standard VAT rates (as of 2026) for correct net-of-VAT figures sent
// to DFI (DfiOrderHandler::resolveVatRate(), based on the invoice address'
// country). All other countries default to 0% - exports outside the EU are
// VAT-exempt. Double-check RO/SK if rates have changed since - both saw
// increases in 2025 and are the least certain of this list.
$euVatRates = [
    'AT' => 20.00, 'BE' => 21.00, 'BG' => 20.00, 'HR' => 25.00, 'CY' => 19.00,
    'CZ' => 21.00, 'DK' => 25.00, 'EE' => 22.00, 'FI' => 25.50, 'FR' => 20.00,
    'DE' => 19.00, 'GR' => 24.00, 'HU' => 27.00, 'IE' => 23.00, 'IT' => 22.00,
    'LV' => 21.00, 'LT' => 21.00, 'LU' => 17.00, 'MT' => 18.00, 'NL' => 21.00,
    'PL' => 23.00, 'PT' => 23.00, 'RO' => 21.00, 'SK' => 23.00, 'SI' => 22.00,
    'ES' => 21.00, 'SE' => 25.00,
];

$sql = \rex_sql::factory();

foreach ($euVatRates as $iso2 => $rate) {
    $sql->setQuery(
        'UPDATE ' . $sql->escapeIdentifier($table) . ' SET vat_rate = :rate WHERE iso2 = :iso2 AND vat_rate IS NULL',
        ['rate' => $rate, 'iso2' => $iso2]
    );
}

$sql->setQuery('UPDATE ' . $sql->escapeIdentifier($table) . ' SET vat_rate = 0 WHERE vat_rate IS NULL');
