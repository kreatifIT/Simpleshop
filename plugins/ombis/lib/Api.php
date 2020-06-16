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
use Kreatif\WSConnector;
use Kreatif\WSConnectorException;


class Api extends WSConnector
{

    public static function testConnection()
    {
        $apiUrl  = Settings::getValue('api_base_url', 'Ombis');
        $apiUser = Settings::getValue('api_username', 'Ombis');
        $apiPwd  = Settings::getValue('api_password', 'Ombis');
        $apiPort = Settings::getValue('api_port', 'Ombis');

        $conn = new parent($apiUrl);
        $conn->setAuthType(CURLAUTH_BASIC);
        $conn->setAuth($apiUser, $apiPwd);
        $conn->setPort($apiPort);
        $conn->setLang('de-De');
        $conn->setGzip(true);
        $conn->setReturnHeader(true);
        $conn->setDebug(true);

        try {
        $response = $conn->request('/versioninfo', [
            'json' => 1
        ]);
        pr($response);
        } catch (WSConnectorException $ex) {
            pr($ex->getMessage(), 'red');
        }
    }
}