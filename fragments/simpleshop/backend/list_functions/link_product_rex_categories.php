<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 14.02.19
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;


?>

<?php if (!\rex_request::isXmlHttpRequest()): ?>
    <div id="linking-container">
        <label>Kategorie auswählen</label>
        <?= \rex_var_link::getWidget('rex_category', 'rex_category', 0) ?>

        <div class="pjax-container"></div>
    </div>

<?php else: ?>
    <?php

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $steps  = 200;
    $page   = rex_get('page', 'int', 0);
    $offset = $page * $steps;
    $search = rex_get('search', 'string');
    $cat_id = rex_get('cat_id', 'int');
    $stmt   = Product::query();

    if (strlen($search)) {
        $stmt->whereRaw('(
            name_1 LIKE :term
            OR code LIKE :term
        )', ['term' => "%{$search}%"]);
    }
    $count      = $stmt->count();
    $collection = $stmt->limit($offset, $steps)
        ->find();

    ?>
    <div class="products" style="margin-top:20px;">
        <div class="form-group">
            <label>Suche</label>
            <input class="form-control" type="text" value="<?= $search ?>" onkeyup="Simpleshop.showFunctionListItems(this, 'search');">
        </div>

        <?php if (count($collection)): ?>
            <?php
            $paging = \Kreatif\Utils::getPagination($count, $steps);
            ?>

            <?php if ($paging): ?>
                <nav class="rex-nav-pagination">
                    <ul class="pagination">
                        <?php foreach ($paging as $item): ?>
                            <?php if ($item['type'] == 'ellipsis'): ?>
                                <li class="disabled">
                                    <span>…</span>
                                </li>
                            <?php else: ?>
                                <li class="rex-page <?= $item['type'] == 'active' ? 'active' : '' ?>">
                                    <a href="javascript:;" onclick="Simpleshop.showFunctionListItems(this, 'paging');" data-page="<?= $item['page'] ?>">
                                        <?= $item['text'] ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <div class="rex-pagination-count"><?= $this->i18n('list_rows_found', $count) ?></div>
                </nav>
            <?php endif; ?>

            <ul class="list-group checklist" style="margin-top:10px;">
                <?php foreach ($collection as $item): ?>
                    <?php
                    $categories = $item->getArrayValue('category');
                    ?>
                    <li class="list-group-item <?= in_array($cat_id, $categories) ? 'active' : '' ?>">
                        <label>
                            <input type="checkbox" name="products[]" value="<?= $item->getId() ?>" onchange="Simpleshop.selectFunctionListItem(this)" <?= in_array($cat_id, $categories) ? 'checked="checked"' : '' ?>>
                            <?= $item->getName() ?>
                            (<?= $item->getValue('code') ?>)
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($paging): ?>
                <nav class="rex-nav-pagination">
                    <ul class="pagination">
                        <?php foreach ($paging as $item): ?>
                            <?php if ($item['type'] == 'ellipsis'): ?>
                                <li class="disabled">
                                    <span>…</span>
                                </li>
                            <?php else: ?>
                                <li class="rex-page <?= $item['type'] == 'active' ? 'active' : '' ?>">
                                    <a href="javascript:;" onclick="Simpleshop.showFunctionListItems(this, 'paging');" data-page="<?= $item['page'] ?>">
                                        <?= $item['text'] ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <div class="rex-pagination-count"><?= $this->i18n('list_rows_found', $count) ?></div>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-info" role="alert">No item found</div>
        <?php endif; ?>
    </div>
<?php endif; ?>
