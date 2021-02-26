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

namespace FriendsOfREDAXO\Simpleshop\Ombis\Customer;


use FriendsOfREDAXO\Simpleshop\CustomerAddress;
use FriendsOfREDAXO\Simpleshop\Ombis\Api;
use FriendsOfREDAXO\Simpleshop\Settings;


class Customer
{
    public static function write(\FriendsOfREDAXO\Simpleshop\Customer $customer, CustomerAddress $invoiceAddress, CustomerAddress $shippingAddress)
    {
        $shippingAddrIds = [];
        $countryId       = $invoiceAddress->getValue('country');
        $postal          = (int)$invoiceAddress->getValue('postal');

        $invoiceAddress = Address::write($invoiceAddress);
        $invoiceAddress->save();

        if ($invoiceAddress->getId() != $shippingAddress->getId()) {
            $shippingAddress = Address::write($shippingAddress);
            $shippingAddress->save();
            $shippingAdrId = $shippingAddress->getValue('ombis_id');
        }

        $customerSettings   = Settings::getValue('ombis_customer_settings', 'Ombis');
        $taxGroupSettings   = Settings::getValue('ombis_tax_group', 'Ombis');
        $statsGroupSettings = Settings::getValue('ombis_statistic_group', 'Ombis');
        $gebieteSettings    = Settings::getValue('ombis_gebiete', 'Ombis');

        if ($countryId == 93 && $postal >= 39000 && $postal <= 39999) {
            $statsGroup = $statsGroupSettings['southtyrol'];
        } else if (isset($statsGroupSettings[$countryId])) {
            $statsGroup = $statsGroupSettings[$countryId];
        } else {
            $statsGroup = $statsGroupSettings['default'];
        }

        if ($countryId == 93) {
            $kontingentGebiet = $customerSettings['kontingentgebiet_inland'];
        } else {
            $kontingentGebiet = $customerSettings['kontingentgebiet_ausland'];
        }


        $data = \rex_extension::registerPoint(new \rex_extension_point('Ombis.customerData', [
            'Fields' => [
                'Rechtssitz'                   => (string)$invoiceAddress->getValue('ombis_id'),
                'Kontingentgebiet'             => (string)$kontingentGebiet,
                'MwStGruppe'                   => $taxGroupSettings[$countryId] ?: $taxGroupSettings['default'],
                'Sammelkontogruppe'            => (string)$customerSettings['sammelkontogruppe'],
                'Buchungsgruppe'               => (string)$customerSettings['buchungsgruppe'],
                'Verkaeufer'                   => (string)$customerSettings['seller'],
                'Branche'                      => (string)$customerSettings['branche'],
                'Verkaufsgebiet'               => (string)$gebieteSettings[$countryId],
                'KuLiStatistikgruppe1'         => (string)$statsGroup,
                'PeriodizitaetRechnungslegung' => 'Manuell',
                // todo: sollen alle Werte auch bei Aktualisierungen geschrieben werden?
            ],
        ], [
            'customer' => $customer,
            'address'  => $invoiceAddress,
        ]));

        $ombisId = (int)$customer->getValue('ombis_id');

        if ($ombisId > 0) {
            $path   = "/{$ombisId}";
            $method = 'PUT';

            $response = Api::curl("/kunde/{$ombisId}", [
                'colls'   => 'Lieferadresse(fields=ID,Adresse.ID)',
                'reduced' => 1,
            ], 'GET', ['ID']);

            foreach ($response['Collections']['Lieferadresse']['Data'] as $_item) {
                $shippingAddrIds[] = $_item['Fields']['Adresse.ID'];
            }
        } else {
            $path   = '';
            $method = 'POST';
        }

        if ($shippingAdrId && !in_array($shippingAdrId, $shippingAddrIds)) {
            $data['Collections'] = [
                'Lieferadresse' => [
                    'Data' => [
                        [
                            'Fields' => ['Adresse' => (string)$shippingAdrId],
                        ],
                    ],
                ],
            ];
        }

        $response = Api::curl('/kunde' . $path, $data, $method);

        if (isset($response['last_id'])) {
            $ombisId = $response['last_id'];
            $customer->setValue('ombis_id', $ombisId);
        }
        return [$customer, $invoiceAddress, $shippingAddress];
    }
}