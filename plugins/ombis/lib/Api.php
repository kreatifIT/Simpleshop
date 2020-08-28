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

namespace FriendsOfREDAXO\Simpleshop\Ombis;


use FriendsOfREDAXO\Simpleshop\Settings;
use Kreatif\Model;
use Kreatif\WSConnector;
use Kreatif\WSConnectorException;
use function FriendsOfREDAXO\Simpleshop\Utils;


class Api extends WSConnector
{

    public static function curl($path, $data = [], $method = 'GET', $fields = [])
    {
        $apiUrl  = Settings::getValue('api_base_url', 'Ombis');
        $apiUser = Settings::getValue('api_username', 'Ombis');
        $apiPwd  = Settings::getValue('api_password', 'Ombis');


        if ($path != '/versioninfo') {
            $apiUrl .= Settings::getValue('api_company_path', 'Ombis');

            if ($method == 'POST' || $method == 'PUT') {
                $data = json_encode($data);
            } else {
                $data = array_merge([
                    'json' => 1,
                ], $data);
                if ($fields) {
                    $data['fields'] = implode(',', $fields);
                }
            }
        }

        $conn = new parent($apiUrl);
        $conn->setAuthType(CURLAUTH_DIGEST);
        $conn->setAuth($apiUser, $apiPwd);
        $conn->setLang('de-De');
        $conn->setGzip(true);
        $conn->setReturnHeader($method == 'POST' || $method == 'PUT');
        //$conn->setDebug(true);

        $response = $conn->request($path, $data, $method);
        return ($method == 'POST' || $method == 'PUT') ? $response : $response['response'];
    }

    public static function testConnection()
    {
        try {
            $response = self::curl('/versioninfo');
            $result   = isset($response['JarSize']) && $response['JarSize'] > 0 ? 'OK' : 'FAILED';
        } catch (WSConnectorException $ex) {
            $result = $ex->getMessage();
        }
        return $result;
    }
}