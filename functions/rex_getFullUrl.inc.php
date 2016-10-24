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

if (!function_exists('rex_getFullUrl'))
{
    function rex_getFullUrl($id = NULL, $clang_id = NULL, $params = [])
    {
        $base_url = '';

        if (count(\rex_yrewrite::getDomains()) > 1)
        {
            $base_url = \rex_yrewrite::getFullPath();
        }
        return $base_url . ltrim(rex_getUrl($id, $clang_id, $params), '/');
    }
}
