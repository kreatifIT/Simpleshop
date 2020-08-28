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
            ], 'GET', ['Code']);
            pr($countryData);
            if (isset($countryData['Data'][0])) {
                $ombisCountryId = $countryData['Data'][0]->Fields->Code;
            }
        }


        $data = [
            'Fields' => [
                'Name1'        => $address->getName(),
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


        $ombisId = trim($address->getValue('ombis_id'));
        $path    = $ombisId == '' ? '' : "/{$ombisId}";
        $method  = $ombisId == '' ? 'POST' : 'PUT';

        //pr('write address: ' . $method, 'orange');
        //pr($data, 'blue');
        //pr(json_encode($data));

        $response = Api::curl('/adresse' . $path, $data, $method);
        return $response['Data'];
    }

    public static function findByEmail($email, $fields = [])
    {
        $response = (array)Api::curl('/adresse', [
            'filter' => "and(eq(Gesperrt,0),eq(EMail, {$email}))",
            'order'  => '-ID',
        ], 'GET', $fields);
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

    public static function findOrCreateByAddress(CustomerAddress $address, $type = 'invoice')
    {
        $fields    = ['ID', 'Name', 'UUID', 'PLZ', 'Ort', 'Strasse1', 'Strasse2', 'EMail', 'Steuernummer', 'MwStNummer', 'UStIDNummer'];
        $isCompany = $address->getValue('ctype') == 'company';
        $customer  = $address->valueIsset('customer_id') ? Customer::get($address->getValue('customer_id')) : null;

        if ($customer) {
            $data = Address::findByEmail($customer->getValue('email'), $fields);
        }
        if (!$data) {
            $fiscalInfo = $isCompany ? $address->getValue('vat_num') : $address->getValue('fiscal_code');
            $data       = Address::findByFiscalInfo($fiscalInfo, $fields);
        }

        if ($data) {
            $_address = null;
            foreach ($data as $item) {
                if ($type == 'invoice') {
                    $vatNum = strtoupper(trim($address->getValue('vat_num')));
                    $fiscal = strtoupper(trim($address->getValue('fiscal_code')));

                    if ($vatNum == '' && $fiscal == '') {
                        $_address = $item;
                    } else if ($vatNum != '' && ($vatNum == strtoupper($item->Fields->Steuernummer) || $vatNum == $item->Fields->MwStNummer || $vatNum == strtoupper($item->Fields->UStIDNummer))) {
                        $_address = $item;
                    } else if ($fiscal != '' && ($fiscal == strtoupper($item->Fields->Steuernummer) || $fiscal == $item->Fields->MwStNummer || $fiscal == strtoupper($item->Fields->UStIDNummer))) {
                        $_address = $item;
                    } else if ($item->Fields->Steuernummer == '' && $item->Fields->MwStNummer == '') {
                        $_address = $item;
                    }
                    if ($_address && $item->Fields->PLZ == $address->getValue('postal') && $item->Fields->Ort == $address->getValue('location') && ($item->Fields->Strasse1 == $address->getValue('street') || $item->Fields->Strasse2 == $address->getValue('street'))) {
                        break;
                    }
                } else {
                    if ($item->Fields->PLZ == $address->getValue('postal') && $item->Fields->Ort == $address->getValue('location') && ($item->Fields->Strasse1 == $address->getValue('street') || $item->Fields->Strasse2 == $address->getValue('street'))) {
                        $_address = $item;
                        break;
                    }
                }
            }
        }

        if (!$_address) {
            self::write($address);
        }

        $address->setValue('ombis_id', $_address->Fields->ID);
        $address->setValue('ombis_uid', $_address->Fields->UUID);
        $address->save();
        return $_address;
    }
}