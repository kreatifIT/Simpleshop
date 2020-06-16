<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 20.05.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;


class Settings
{
    public static function getValue($key, $plugin = '')
    {
        $plugin   = $plugin == '' ? '' : '.' . $plugin;
        $settings = \rex::getConfig('simpleshop.Settings' . $plugin);

        return $settings[$key];
    }

    public static function getArticle($key)
    {
        $linklist = self::getValue('linklist');
        $article = isset($linklist[$key]) ? \rex_article::get($linklist[$key]) : null;

        if ($article && !$article->isOnline()) {
            $article = null;
        }
        return $article;
    }
}