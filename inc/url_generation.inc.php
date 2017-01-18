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

\rex_extension::register('URL_GENERATOR_PATH_CREATED', function ($params)
{
    $path    = $params->getSubject();
    $data    = $params->getParam('data');
    $lang_id = $params->getParam('clang_id');

    switch ($params->getParam('table')->name)
    {
        case Category::TABLE:
            $path = Category::get($data['id'])->generatePath($lang_id);
            break;
        case Product::TABLE:
            $path = Product::get($data['id'])->generatePath($lang_id, $path);
            break;
    }
    return $path;
});