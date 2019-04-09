<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 09.04.19
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$cat_name   = $this->getVar('cat_name');
$catId      = $this->getVar('catId');
$collection = $this->getVar('collection');
$title      = str_replace('{CATNAME}', $cat_name, rex_i18n::msg('label.sort_products_for_cat_title'));

?>
<?= rex_view::title($title, '') ?>

<div id="sort-container">
    <table class="table table-striped sortable-list">
        <thead>
        <tr>
            <th style="width:50px;">&nbsp;</th>
            <th>ID</th>
            <th>Name</th>
            <th>Code</th>
            <th>Preis</th>
        </tr>
        </thead>

        <?php foreach ($collection as $item): ?>
            <tr>
                <td class="sort-handle">
                    <i class="rex-icon fa fa-bars sort-icon"
                       data-url="<?= rex_url::currentBackendPage(['id' => $catId]) ?>"
                       data-id="<?= $item->getValue('relation_id') ?>"
                       data-table-sort-field="prio"
                       data-table-sort-order="asc"></i>
                </td>
                <td><?= $item->getValue('relation_id') ?></td>
                <td><?= $item->getName() ?></td>
                <td><?= $item->getValue('code') ?></td>
                <td><?= $item->getValue('price') ?></td>
            </tr>
        <?php endforeach; ?>

    </table>

    <?php if (count($collection) == 0): ?>
        <div style="padding:20px 0;text-align:center;">
            <h4><?= rex_i18n::msg('label.no_product_available') ?></h4>
        </div>
    <?php endif; ?>

    <a href="<?= rex_url::backendPage('yform/manager/data_edit', ['table_name' => \FriendsOfREDAXO\Simpleshop\Category::TABLE]) ?>" class="btn btn-primary"><?= rex_i18n::msg('yform_back_to_overview') ?></a>
</div>