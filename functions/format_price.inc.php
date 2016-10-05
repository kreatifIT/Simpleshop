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

function format_price($price)
{
    $conf = localeconv();
    return number_format($price, 2, $conf['mon_decimal_point'], $conf['mon_thousands_sep']);
}