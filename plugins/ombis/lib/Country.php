<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 11.01.21
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop\Ombis;


class Country
{


    public static function getId(\FriendsOfREDAXO\Simpleshop\Country $country)
    {
        if (!$country->valueIsset('ombis_id')) {
            $countryData = Api::curl('/land', [
                'filter' => "eq(ISOCode,{$country->getValue('iso2')})",
            ], 'GET', ['ID']);
            if (isset($countryData['Data'][0])) {
                $ombisCountryId = $countryData['Data'][0]['Fields']['ID'];

                $country->setValue('ombis_id', $ombisCountryId);
                $country->save();
            }
        }
        return $country->getValue('ombis_id');
    }
}