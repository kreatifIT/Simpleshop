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

function format_price($price, $decimals = 2)
{
    $conf = localeconv();
    return number_format($price, $decimals, $conf['mon_decimal_point'] ?: $conf['decimal_point'], $conf['mon_thousands_sep'] ?: $conf['thousands_sep']);
}

function format_date($datetime)
{
    return format_timestamp(strtotime($datetime));
}

function format_timestamp($timestamp, $withTime = TRUE, $id = NULL)
{
    $locale    = setlocale(LC_ALL, 0);
    $datetype  = IntlDateFormatter::LONG;
    $timetype  = $withTime ? IntlDateFormatter::SHORT : IntlDateFormatter::NONE;

    rex_extension::registerPoint(new rex_extension_point('format_timestamp.datetype', $datetype, ['id' => $id]));
    rex_extension::registerPoint(new rex_extension_point('format_timestamp.timetype', $timetype, ['id' => $id]));

    $formatter = new IntlDateFormatter($locale, $datetype, $timetype);
    return $formatter->format($timestamp);
}