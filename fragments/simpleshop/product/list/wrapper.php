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
$element_count  = $this->getVar('element_count', 20);
$filter         = $this->getVar('filter', []);
$offset         = $element_count * (int) rex_request('page', 'int', 0);
$orderby        = $this->getVar('orderby');
$order          = $this->getVar('order');
$order_values   = $this->getVar('order_values');
$cat_path       = $this->getVar('cat_path');
$sidebar        = $this->getVar('sidebar_content');

// prevent malformed queries
$orderby = in_array($orderby, ['price', 'prio']) ? $orderby : 'prio';
$order   = $order == 'asc' ? $order : 'desc';

?>
<div class="row column margin-top">
    <h1 class="heading"><span>###label.online_shop_strong###</span></h1>
</div>
<div class="row">
    <div class="shop-sidebar large-3 columns margin-bottom">
        <?= $sidebar ?>
    </div>

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
                                <option value="<?= $value ?>" <?php if ($order_value['is_active'])
                                {
                                    echo 'selected="selected"';
                                } ?>><?= $order_value['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Popup -->
        <?php
        $this->subfragment('simpleshop/product/general/cart/popup-wrapper.php');
        ?>

        <div class="shop-products-grid <?= $this->getVar('grid-class') ?>">
            <?php
            $query = Product::query()
                ->alias('m')
                ->where('m.status', 1)
                ->whereRaw('m.type = :type1 OR m.type = :type2', ['type1' => 'product', 'type2' => 'product_request'])
                ->leftJoin(Tax::TABLE, 'jt1', 'jt1.id', 'm.tax')
                ->orderBy($orderby, $order);

            foreach ($filter as $values)
            {
                // SEARCH //////////////////////////////////////////////////////////////////////
                if ($values[0] == 'search')
                {
                    $where = [];
                    foreach ($values[1][0] as $column)
                    {
                        $where[] = "m.{$column} LIKE :wr1";
                    }
                    $query->whereRaw("(" . implode(' OR ', $where) . ")", ['wr1' => $values[1][1]]);
                }
                // CATEGORY ////////////////////////////////////////////////////////////////////
                else if ($values[0] == 'price_range')
                {
                    $query->whereRaw("((m.price * (jt1.tax / 100 + 1)) BETWEEN :bt1 AND :bt2 OR (m.reduced_price > 0 AND (m.reduced_price * (jt1.tax / 100 + 1)) BETWEEN :bt1 AND :bt2))", [
                        'bt1' => $values[1][0],
                        'bt2' => $values[1][1],
                    ]);
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
                    call_user_func_array($values[0], [$query]);
                }
            }
//                    pr($query->getQuery());
//                    exit;
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

            if (count($products) == 0):
                ?>
                <div class="text-center margin-top">
                    <p>###shop.no_products_available###</p>
                    <a href="?action=filter">###action.reset_filter###</a>
                </div>
            <?php endif; ?>
        </div>
        <?php
        if ($has_pagination)
        {
            $this->subfragment('simpleshop/product/list/pagination.php');
        }
        ?>
    </div>
</div>