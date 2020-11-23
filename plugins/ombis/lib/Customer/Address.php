<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 15/06/2020
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop\Ombis\Customer;


use FriendsOfREDAXO\Simpleshop\Country;
use FriendsOfREDAXO\Simpleshop\Customer;
use FriendsOfREDAXO\Simpleshop\CustomerAddress;
use FriendsOfREDAXO\Simpleshop\Ombis\Api;


class Address
{


    public static function getData($ombisId)
    {
        return (array)Api::curl("/adresse/{$ombisId}");
    }

    public static function write(CustomerAddress $address)
    {
        $isCompany      = $address->getValue('ctype') == 'company';
        $customer       = $address->valueIsset('customer_id') ? Customer::get($address->getValue('customer_id')) : null;
        $langId         = $customer ? $customer->getValue('lang_id') : $address->getValue('lang_id');
        $lang           = $langId ? \rex_clang::get($langId) : null;
        $countryId      = $address->getValue('country');
        $country        = $countryId ? Country::get($countryId) : null;
        $ombisCountryId = '';

        if ($country) {
            $countryData = Api::curl('/land', [
                'filter' => "eq(ISOCode,{$country->getValue('iso2')})",
            ], 'GET', ['ID']);
            if (isset($countryData['Data'][0])) {
                $ombisCountryId = $countryData['Data'][0]->Fields->ID;
            }
        }


        $data = [
            'Fields' => [
                'Name1'        => $isCompany ? $address->getName() : $address->getValue('lastname'),
                'Name2'        => $isCompany ? '' : $address->getValue('firstname'),
                'Email'        => $customer ? $customer->getValue('email') : $address->getValue('email'),
                'Sprache'      => $lang ? $lang->getCode() : '',
                'Steuernummer' => strtoupper($address->getValue('fiscal_code')),
                'PLZ'          => (string)$address->getValue('postal'),
                'Land'         => (string)$ombisCountryId,
                'Ort'          => $address->getValue('location'),
                'Strasse1'     => $address->getValue('street'),
                'Strasse2'     => (string)$address->getValue('street_additional'),
                'Mobiltelefon' => (string)$address->getValue('phone'),
            ],
        ];

        if ($isCompany) {
            $data['Fields']['UStIDNummer'] = strtoupper($address->getValue('vat_num'));
            $data['Fields']['Geschlecht']  = 'legalPerson';
        }


        $ombisId  = trim($address->getValue('ombis_id'));
        $path     = $ombisId == '' || $ombisId == 0 ? '' : "/{$ombisId}";
        $method   = $path == '' ? 'POST' : 'PUT';
        $response = Api::curl('/adresse' . $path, $data, $method);

        if (isset($response['last_id'])) {
            $ombisId = $response['last_id'];
        }
        $response = Api::curl("/adresse/{$ombisId}", [], 'GET', ['ID', 'UUID']);

        $address->setValue('ombis_id', $response['Fields']->ID);
        $address->setValue('ombis_uid', $response['Fields']->UUID);

        return $address;
    }

    public static function findByEmail($email, $fields = [])
    {
        $response = (array)Api::curl('/kommunikation', [
            'filter' => "and(eq(Typ,E1),eq(NummerAdresse,{$email}))",
            'order'  => '-ID',
        ], 'GET', ['Adresse.ID']);

        if (isset($response['Data'])) {
            $addressIds = [];
            foreach ($response['Data'] as $item) {
                $addressIds[] = $item->Fields->{'Adresse.ID'};
            }
            $response = (array)Api::curl('/adresse', [
                'filter' => 'in(ID,' . implode(',', $addressIds) . ')',
                'order'  => '-ID',
            ], 'GET', $fields);
        }
        return $response['Data'];
    }

    public static function findByFiscalInfo($fiscalInfo, $fields = [])
    {
        $response = (array)Api::curl('/adresse', [
            'filter' => "or(eq(Steuernummer,{$fiscalInfo}),eq(UStIDNummer,{$fiscalInfo}))",
            'order'  => 'ID',
        ], 'GET', $fields);
        return $response['Data'];
    }

    public static function findByAddressInfo($name, $postal, $location, $fields = [])
    {
        $response = null;
        $filter   = array_filter([
            trim($postal) == '' ? '' : "eq(PLZ,'" . addslashes($postal) . "')",
            trim($location) == '' ? '' : "eq(Ort,'" . addslashes($location) . "')",
            trim($name) == '' ? '' : "like(Suchbegriff,'" . addslashes($name) . "')",
        ]);

        if (count($filter)) {
            $filterString = count($filter) > 1 ? 'and(' . implode(',', $filter) . ')' : current($filter);
            $_response    = (array)Api::curl('/adresse', [
                'filter' => $filterString,
                'order'  => 'ID',
            ], 'GET', $fields);
            $response     = $_response['Data'];
        }
        return $response;
    }

    public static function findOrCreateByAddress(CustomerAddress $address, $type = 'invoice')
    {
        $data       = [];
        $fields     = ['ID', 'Name', 'UUID', 'PLZ', 'Ort', 'Strasse1', 'Strasse2', 'EMail', 'Steuernummer', 'MwStNummer', 'UStIDNummer'];
        $isCompany  = $address->getValue('ctype') == 'company';
        $customer   = $address->valueIsset('customer_id') ? Customer::get($address->getValue('customer_id')) : null;
        $fiscalInfo = trim($isCompany ? $address->getValue('vat_num') : $address->getValue('fiscal_code'));

        if ($customer) {
            $data = Address::findByEmail($customer->getValue('email'), $fields);
        }
        if ($fiscalInfo == '') {
            $data = array_merge($data, (array)Address::findByAddressInfo($address->getName(), $address->getValue('postal'), $address->getValue('location'), $fields));
        } else {
            $data = array_merge($data, Address::findByFiscalInfo($fiscalInfo, $fields));
        }

        if (count($data)) {
            $_address = null;
            foreach ($data as $item) {
                if ($type == 'invoice') {
                    $vatNum = strtoupper(trim($address->getValue('vat_num')));
                    $fiscal = strtoupper(trim($address->getValue('fiscal_code')));

                    if ($vatNum == '' && $fiscal == '') {
                        $__address = $item;
                    } else if ($vatNum != '' && ($vatNum == strtoupper($item->Fields->Steuernummer) || $vatNum == $item->Fields->MwStNummer || $vatNum == strtoupper($item->Fields->UStIDNummer))) {
                        $__address = $item;
                    } else if ($fiscal != '' && ($fiscal == strtoupper($item->Fields->Steuernummer) || $fiscal == $item->Fields->MwStNummer || $fiscal == strtoupper($item->Fields->UStIDNummer))) {
                        $__address = $item;
                    } else if ($item->Fields->Steuernummer == '' && $item->Fields->MwStNummer == '') {
                        $__address = $item;
                    }
                    if ($__address && $item->Fields->PLZ == $address->getValue('postal') && $item->Fields->Ort == $address->getValue('location') && ($item->Fields->Strasse1 == $address->getValue('street') || $item->Fields->Strasse2 == $address->getValue('street'))) {
                        $_address = $__address;
                        break;
                    }
                } else {
                    $_ombisPLZ     = strtoupper(trim($item->Fields->PLZ));
                    $_ombisOrt     = strtoupper(trim($item->Fields->Ort));
                    $_ombisStreet1 = strtoupper(trim($item->Fields->Strasse1));
                    $_ombisStreet2 = strtoupper(trim($item->Fields->Strasse2));

                    $_shopPLZ    = strtoupper(trim($address->getValue('postal')));
                    $_shopOrt    = strtoupper(trim($address->getValue('location')));
                    $_shopStreet = strtoupper(trim($address->getValue('street')));

                    if (($_shopPLZ == '' || $_shopPLZ == $_ombisPLZ) && ($_shopOrt == '' || $_shopOrt == $_ombisOrt) && ($_shopStreet == '' || $_shopStreet == $_ombisStreet1 || $_shopStreet == $_ombisStreet2)) {
                        $_address = $item;
                        break;
                    }
                }
            }
        }

        if ($_address) {
            $address->setValue('ombis_id', $_address->Fields->ID);
            $address->setValue('ombis_uid', $_address->Fields->UUID);
        } else {
            $address = self::write($address);
        }
        $address->save();
        return $address;
    }

    public static function ext__yformDataUpdated(\rex_extension_point $ep)
    {
        $table = $ep->getParam('table');
        // todo: do a better check
        return;

        if ($table->getTableName() == CustomerAddress::TABLE) {
            $yform         = $ep->getSubject();
            $oldData       = $ep->getParam('old_data');
            $object        = $ep->getParam('data');
            $fieldsToCheck = \rex_extension::registerPoint(new \rex_extension_point('Ombis.AddressFieldsToCheck', [
                'ctype',
                'company_name',
                'firstname',
                'lastname',
                'fiscal_code',
                'vat_num',
                'street',
                'street_additional',
                'postal',
                'location',
                'country',
            ]));

            foreach ($object->getData() as $name => $value) {
                if (in_array($name, $fieldsToCheck) && $oldData[$name] !== $value) {
                    $sql = \rex_sql::factory();
                    $sql->setTable(CustomerAddress::TABLE);
                    $sql->setValue('ombis_id', null);
                    $sql->setValue('ombis_uid', null);
                    $sql->setWhere(['id' => $object->getId()]);
                    $sql->update();

                    $object->setValue('ombis_id', null);
                    $object->setValue('ombis_uid', null);

                    $formData = [];
                    foreach ($object->getData() as $_name => $_value) {
                        $formData[] = "{$_name}|{$_value}";
                    }
                    $yform->setFormData(implode("\n", $formData));
                    break;
                }
            }
        }
    }
}