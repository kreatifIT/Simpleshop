<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 * @author jan.kristinus@yakamara.de
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

$yform   = $this->getVar('yform');
$address = $this->getVar('address');


$yform->setValueField('text', [
    'name'    => 'customer_address.2.firstname',
    'label'   => '###label.firstname###',
    'default' => $address->getValue('firstname'),
]);

$yform->setValueField('text', [
    'name'    => 'customer_address.2.lastname',
    'label'   => '###label.lastname###',
    'default' => $address->getValue('lastname'),
]);

$yform->setValueField('text', [
    'name'    => 'customer_address.2.additional',
    'label'   => '###label.addition###',
    'default' => $address->getValue('additional'),
]);

$yform->setValueField('text', [
    'name'    => 'customer_address.2.street',
    'label'   => '###label.street###',
    'default' => $address->getValue('street'),
]);

$yform->setValueField('text', [
    'name'    => 'customer_address.2.location',
    'label'   => '###label.location###',
    'default' => $address->getValue('location'),
]);

$yform->setValueField('text', [
    'name'    => 'customer_address.2.zip',
    'label'   => '###label.postal###',
    'default' => $address->getValue('zip'),
]);

$yform->setHiddenField('id_2', $address->getValue('id'));