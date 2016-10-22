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

function checkstr($string, $default = '')
{
    if (strlen(trim($string)) == '') {
        $string = $default;
    }
    return $string;
}