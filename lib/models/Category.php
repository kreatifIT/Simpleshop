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


use Url\Url;


class Category extends Model
{
    const TABLE        = 'rex_shop_category';
    const URL_PARAMKEY = 'category_id';

    private $parents = null;

    public static function getTree($parent_id = '', $ignoreOffline = true)
    {
        $query = parent::query()
            ->where('parent_id', $parent_id);

        if ($ignoreOffline) {
            $query->where('status', 1);
        }
        $collection = $query->find();

        foreach ($collection as $coll) {
            $coll->children = self::getTree($coll->getValue('id'), $ignoreOffline);
        }
        return $collection;
    }

    public function getParentTree($force = false)
    {
        if ($this->parents === null) {
            $this->parents = [$this];
            $parent_id     = $this->getValue('parent_id');

            if ($parent_id) {
                $this->parents = array_merge(self::get($parent_id)
                    ->getParentTree(true), $this->parents);
            }
        }
        return $this->parents;
    }

    public function generatePath($lang_id, $path = '')
    {
        $parent_id = $this->getValue('parent_id');
        $path      = Url::getRewriter()
                ->normalize($this->getValue('name_' . $lang_id)) . '/' . $path;

        if ($parent_id) {
            $parent = self::get(self::query()
                ->where('id', $parent_id)
                ->findOne()
                ->getValue('id'));
            $path   = $parent->generatePath($lang_id, $path);
        }
        return $path;
    }

    public static function ext_yform_data_delete($params)
    {
        $result = $params->getSubject();

        if ($result !== false && $params->getParam('table')
                ->getTableName() == self::TABLE
        ) {
            $Addon    = \rex_addon::get('simpleshop');
            $links    = [];
            $name     = 'name_' . \rex_clang::getCurrentId();
            $cat_id   = $params->getParam('data_id');
            $products = Product::query()
                ->resetSelect()
                ->select('id')
                ->select($name)
                ->where('category_id', $cat_id)
                ->find();

            foreach ($products as $product) {
                $links[] = $product->getValue($name) . ' -
                    <a href="' . \rex_url::backendPage('yform/manager/data_edit', ['data_id' => $product->getValue('id'), 'table_name' => Product::TABLE, 'func' => 'edit',]) . '" target="_blank">' . $Addon->i18n('action.edit_product') . '</a> |
                    <a href="' . $product->getUrl() . '" target="_blank">' . $Addon->i18n('action.show_in_frontend') . '</a>
                ';
            }
            // find subcategories
            $categories = Category::query()
                ->resetSelect()
                ->select('id')
                ->select($name)
                ->where('parent_id', $cat_id)
                ->find();

            foreach ($categories as $category) {
                $result = $result && $category->delete();
            }
            if (count($links)) {
                $category = Category::get($cat_id);
                $content  = '<p>' . strtr($Addon->i18n('error.cannot_delete_category'), ['{{name}}' => $category->getValue($name)]) . '</p>';
                $content  .= '<li>' . implode('</li><li>', $links) . '</li>';
                echo \rex_view::warning($content);
                $result = false;
            }
        }
        return $result;
    }

    public static function ext_yform_data_list(\rex_extension_point $ep)
    {
        $list  = $ep->getSubject();
        $table = $ep->getParam('table');
        
        if ($table->getTableName() == self::TABLE && !isset($_GET['rex_yform_manager_opener'])) {
            $list->addColumn('shop_prod_sort', '<i class="rex-icon fa-sort"></i>&nbsp;' . \rex_i18n::msg('label.sort_products'), count($list->getColumnNames()));
            $list->setColumnLabel('shop_prod_sort', '');
            $list->setColumnParams('shop_prod_sort', ['id' => '###id###', 'page' => 'simpleshop/product_sort']);
        }
        return $list;
    }
}