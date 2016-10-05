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

use Url\Generator;
use Url\Url;

class Category extends \rex_yform_manager_dataset
{
    private $parents = NULL;

    public static function getTree($parent_id = '', $ignoreOffline = TRUE)
    {
        $query = parent::query()->where('parent_id', $parent_id);

        if ($ignoreOffline)
        {
            $query->where('status', 1);
        }
        $collection = $query->find();

        foreach ($collection as $coll)
        {
            $coll->children = self::getTree($coll->getValue('id'), $ignoreOffline);
        }
        return $collection;
    }

    public function getParentTree($force = FALSE)
    {
        if ($this->parents === NULL)
        {
            $this->parents = [$this];
            $parent_id     = $this->getValue('parent_id');

            if ($parent_id)
            {
                $this->parents = array_merge(self::get($parent_id)->getParentTree(TRUE), $this->parents);
            }
        }
        return $this->parents;
    }

    public function getUrl($lang_id = NULL)
    {
        return rex_getUrl(NULL, $lang_id, ['category_id' => $this->getValue('id')]);
    }

    public function generatePath($lang_id, $path = '')
    {
        $parent_id = $this->getValue('parent_id');
        $path      = Url::getRewriter()->normalize($this->getValue('name_' . $lang_id)) . '/' . $path;

        if ($parent_id)
        {
            $parent = self::get(self::query()->where('id', $parent_id)->findOne()->getValue('id'));
            $path   = $parent->generatePath($lang_id, $path);
        }
        return $path;
    }
}