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

// TODO: to review
\rex_extension::register('YFORM_DATA_LIST_SQL', function ($params)
{
    $sql   = $params->getSubject();
    $table = $params->getParams()['table'];

    if ($table->getTableName() == Category::TABLE)
    {
        if (stripos($sql, 'where') === FALSE)
        {
            $sql = preg_replace('/ORDER\sBY/i', 'where `parent_id` = \'\' ORDER BY', $sql);
        }
    }
    return $sql;
});