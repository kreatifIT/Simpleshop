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

class VatCheck
{
    public static function validateField($countryISO2, $vatNum)
    {
        if (class_exists('SoapClient')) {
            $client = new \SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl", ['trace' => true]);
            $res    = $client->checkVat([
                'countryCode' => $countryISO2,
                'vatNumber'   => $vatNum,
            ]);

            if ($res->valid != true) {
                $result['is_verified'] = false;

                if ($countryISO2 == 'IT') {
                    self::validateVATPattern($vatNum);
                }
                else {
                    throw new \ErrorException('vat_not_valid');
                }
            }
        }
    }

    protected static function validateVATPattern($vat_num)
    {
        if (strlen($vat_num) != 11) {
            throw new \ErrorException('vat_invalid_length');
        }
        if (preg_match("/^[0-9]+\$/", $vat_num) != 1) {
            throw new \ErrorException('vat_invalid_characters');
        }
        $s = 0;
        for ($i = 0; $i <= 9; $i += 2) {
            $s += ord($vat_num[$i]) - ord('0');
        }
        for ($i = 1; $i <= 9; $i += 2) {
            $c = 2 * (ord($vat_num[$i]) - ord('0'));
            if ($c > 9) {
                $c = $c - 9;
            }
            $s += $c;
        }
        if ((10 - $s % 10) % 10 != ord($vat_num[10]) - ord('0')) {
            throw new \ErrorException('vat_not_valid');
        }
    }
}
