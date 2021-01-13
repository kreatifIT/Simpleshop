<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 12.01.21
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop\Ombis\Customer;


use FriendsOfREDAXO\Simpleshop\Ombis\Api;
use Kreatif\WSConnectorException;


class Kontingentgebiet
{

    public static function getAll($fields = [])
    {
        $response = [];
        try {
            $response = (array)Api::curl('/kontingentgebiet/', [], 'GET', $fields)['Data'];
        } catch (WSConnectorException $ex) {
        }
        return $response;
    }
}