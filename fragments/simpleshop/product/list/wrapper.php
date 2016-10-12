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

$has_pagination = $this->getVar('has_pagination');
$element_count  = $this->getVar('element_count') ? $this->getVar('element_count') : 20;
$filter         = $this->getVar('filter') ? $this->getVar('filter') : [];
$offset         = $element_count * (int) rex_request('page', 'int', 0);
$orderby        = $this->getVar('orderby');
$order          = $this->getVar('order');
$order_values   = $this->getVar('order_values');
$cat_path       = $this->getVar('cat_path');

// prevent malformed queries
$orderby = in_array($orderby, ['price', 'createdate']) ? $orderby : 'createdate';
$order   = $order == 'desc' ? $order : 'asc';

?>
<div class="<?= $this->getVar('class') ?>">

    <?php if ($this->getVar('has_infobar')): ?>
        <div class="shop-products-info-bar clearfix">

            <?php if (count($cat_path)): ?>
            <div class="breadcrumbs">
                <ul class="rex-breadcrumb">
                <?php foreach ($cat_path as $index => $path):
                    if (isset ($path['label'])):
                ?>
                    <li class="rex-lvl<?= $index + 1 ?>">
                        <?php if (isset($path['url'])): ?><a href="<?= $path['url'] ?>"><?php endif; ?>
                            <span class="label"><?= $path['label'] ?></span>
                        <?php if (isset($path['url'])): ?></a><?php endif; ?>
                    </li>
                <?php
                    endif;
                endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php if ($order_values): ?>
            <div class="select">
                <select name="order" class="shop-list-order">
                    <?php foreach ($order_values as $value => $order_value): ?>
                        <option value="<?= $value ?>" <?php if ($order_value['is_active']) echo 'selected="selected"'; ?>><?= $order_value['label']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Popup -->
    <?php
    $this->subfragment('simpleshop/product/general/cart/popup.php');
    ?>
    
    <div class="shop-products-grid <?= $this->getVar('grid-class') ?>">
        <?php
        $query = \FriendsOfREDAXO\Simpleshop\Product::query()
            ->where('status', 1)
            ->orderBy($orderby, $order);

        foreach ($filter as $values)
        {
            // SEARCH //////////////////////////////////////////////////////////////////////
            if ($values[0] == 'search')
            {
                $where = [];
                foreach ($values[1][0] as $column)
                {
                    $where[] = $column . ' LIKE :wr1';
                }
                $query->whereRaw("(" . implode(' OR ', $where) . ")", ['wr1' => $values[1][1]]);
            }
            // CATEGORY ////////////////////////////////////////////////////////////////////
            else if ($values[0] == 'price_range')
            {
                $query->whereRaw("price BETWEEN :bt1 AND :bt2", ['bt1' => $values[1][0], 'bt2' => $values[1][1]]);
            }
            // CATEGORY ////////////////////////////////////////////////////////////////////
            else if (in_array($values[0], ['category_id']))
            {
                // nothing to do - just pass the values
                $query->where($values[0], $values[1], $values[2] ?: NULL);
            }
            // CALLABLE ////////////////////////////////////////////////////////////////////
            else if (is_callable($values[0]))
            {
                call_user_func($type, $query);
            }
        }
//        pr($query->getQuery());
//        exit;
        if ($has_pagination)
        {
            $total = $query->count();
            $this->setVar('total', $total);

            // prevent page overflow
            if ($offset > 0 && $total <= $offset)
            {
                rex_redirect(\rex_config::get('structure', 'notfound_article_id'));
            }
            $query->limit($offset, $element_count);
        }
        $products = $query->find();

        // include the product items
        foreach ($products as $product)
        {
            $this->setVar('class', 'info', FALSE);
            $this->setVar('product', $product);
            $this->subfragment('simpleshop/product/list/element.php');
        }
        ?>
    </div>
    <?php
    if ($has_pagination)
    {
        $this->subfragment('simpleshop/product/list/pagination.php');
    }
    ?>
</div>
