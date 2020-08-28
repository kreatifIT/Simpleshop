<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 07.08.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop\Ombis;


use Kreatif\WSConnectorException;


class Payment
{

    public static function getAll()
    {
        $response = [];
        try {
            $response = (array)Api::curl('/zahlungsart')['Data'];
        } catch (WSConnectorException $ex) {
        }
        return $response;
    }
}