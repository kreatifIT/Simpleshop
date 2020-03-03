<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 02.03.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

$address = $this->getVar('address');
$Country = $address->valueIsset('country') ? Country::get($address->getValue('country')) : null;


echo $address->getName() . '<br>';

if ($address->valueIsset('street')) {
    echo $address->getValue('street') . '<br>';
}
if ($address->valueIsset('street_additional')) {
    echo $address->getValue('street_additional') . '<br>';
}
if ($address->valueIsset('postal')) {
    echo $address->getValue('postal') . '<br>';
}

$locationRow = [];
if ($address->valueIsset('location')) {
    $locationRow[] = $address->getValue('location');
}
if ($address->valueIsset('province')) {
    $locationRow[] = '- ' . $address->getValue('province');
}
if (count($locationRow)) {
    echo implode(' ', $locationRow) . '<br>';
}
if ($Country) {
    echo $Country->getName() . '<br>';
}
if (in_array($address->getValue('ctype'), ['company', 'person']) && $address->valueIsset('fiscal_code')) {
    echo $address->getValue('fiscal_code') . '<br/>';
}
if ($address->getValue('ctype') == 'company' && $address->valueIsset('vat_num')) {
    echo '###label.vat_short###: ' . $address->getValue('vat_num') . '<br/>';
}

