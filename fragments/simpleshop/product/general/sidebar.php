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


$object_id   = \rex::getProperty('url_object_id');
$object_data = \rex::getProperty('url_object_data');
$label_name  = sprogfield('name');
$categories  = Category::getTree();
$max_price   = Product::query()
    ->resetSelect()->selectRaw('MAX(price)', 'max')
    ->select('id')
    ->findOne()
    ->getValue('max');
$quotient    = (strlen(round($max_price)) - 1) * 10;
$max_price   = ceil($max_price / $quotient) * $quotient;

if (rex_get('action', 'string') == 'filter')
{
    $_SESSION['view']['price_from']   = (int) rex_request('price_from', 'int', 0);
    $_SESSION['view']['price_to']     = (int) rex_request('price_to', 'int', 0);
    $_SESSION['view']['quality_appr'] = (int) rex_request('quality_appr', 'int', 0);
    $_SESSION['view']['offers']       = (int) rex_request('offers', 'int', 0);
}
else
{
    $_SESSION['view']['price_from']   = from_array($_SESSION['view'], 'price_from', 0);
    $_SESSION['view']['price_to']     = from_array($_SESSION['view'], 'price_to', 'max');
    $_SESSION['view']['quality_appr'] = from_array($_SESSION['view'], 'quality_appr', 0);
    $_SESSION['view']['offers']       = from_array($_SESSION['view'], 'offers', 0);
}
if ($_SESSION['view']['price_to'] == $max_price)
{
    $_SESSION['view']['price_to'] = 'max';
}
$price_to = $_SESSION['view']['price_to'] == 'max' ? $max_price : $_SESSION['view']['price_to'];

if ($object_id && $object_data->urlParamKey == 'category_id')
{
    $Category      = Category::get($object_id);
    $category_tree = $Category->getParentTree();
}

if (!$object_id || $object_data->urlParamKey != 'product_id'): ?>
    <div class="product-filter">
        <h3>###label.extended_search###</h3>
        <form action="" method="get">
            <input name="term" type="text" placeholder="###label.search_placeholder###"
                   value="<?= rex_request('term', 'string', $_SESSION['view']['search_term']) ?>">
            <?php /* ?>
            <div class="select">
                <select name="category_id" id="category">
                    <option value="">###label.category###...</option>
                    <?php foreach ($categories as $category):
                        $product_count = $category->getValue('product_count');
                        $children_count = count($category->getValue('children'));
                        if ($children_count):
                            $children = $category->getValue('children');
                            ?>
                            <optgroup label="<?= $category->getValue($label_name) ?>">
                                <?php foreach ($children as $child):
                                    $product_count = $child->getValue('product_count');
                                    $subchild_count = count($child->getValue('children'));

                                    if ($subchild_count):
                                        $subchildren = $child->getValue('children');
                                        ?>
                                        <optgroup label="<?= $child->getValue($label_name) ?>">
                                            <?php foreach ($subchildren as $subchild): ?>
                                                <option
                                                    value="<?= $subchild->getValue('id') ?>"><?= $subchild->getValue($label_name) ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php elseif ($product_count): ?>
                                        <option
                                            value="<?= $child->getValue('id') ?>"><?= $child->getValue($label_name) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php elseif ($product_count): ?>
                            <option
                                value="<?= $category->getValue('id') ?>"><?= $category->getValue($label_name) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php */ ?>

            <span class="horizontal-rule double-rule"></span>
            <div class="price-filter">
                <h4>###label.filter_price###</h4>
                <div class="slider price-slider" data-slider data-initial-start="<?= $_SESSION['view']['price_from'] ?>"
                     data-initial-end="<?= $price_to ?>" data-step="5" data-end="<?= $max_price ?>">
                        <span class="slider-handle" data-slider-handle role="slider" tabindex="1"
                              aria-controls="slider-output-1"></span>
                    <span class="slider-fill" data-slider-fill></span>
                        <span class="slider-handle" data-slider-handle role="slider" tabindex="1"
                              aria-controls="slider-output-2"></span>
                </div>
                <div class="slider-output-container clearfix">
                    <div class="slider-output min-price">
                        <span>&euro;</span>
                        <input type="text" id="slider-output-1" name="price_from" readonly
                               value="<?= $_SESSION['view']['price_from'] ?>">
                    </div>
                    <div class="slider-output max-price">
                        <span>&euro;</span>
                        <input type="text" id="slider-output-2" name="price_to" readonly value="<?= $price_to ?>">
                    </div>
                </div>
            </div>
            <span class="horizontal-rule double-rule"></span>
            <div class="checkboxes">
                <div class="custom-checkbox">
                    <label>
                        ###custom.quality_filter###
                        <input type="checkbox" name="quality_appr"
                               value="1" <?php if ($_SESSION['view']['quality_appr'] == 1)
                        {
                            echo 'checked="checked"';
                        } ?>/>
                        <span class="checkbox"></span>
                    </label>
                </div>
                <div class="custom-checkbox">
                    <label>
                        ###label.products_on_sale###
                        <input type="checkbox" name="offers" value="1" <?php if ($_SESSION['view']['offers'] == 1)
                        {
                            echo 'checked="checked"';
                        } ?>/>
                        <span class="checkbox"></span>
                    </label>
                </div>
            </div>
            <span class="horizontal-rule double-rule"></span>
            <button class="button" type="submit" name="action" value="filter">###label.show_products###</button>
            <a class="button filter-reset" href="<?= rex_getUrl(NULL, NULL, ['action' => 'filter']) ?>">###action.reset_filter###</a>
        </form>
    </div>
    <span class="horizontal-rule large-rule"></span>
<?php endif; ?>

<div class="shop-accordion-menu product-category">
    <ul>

        <?php foreach ($categories as $category):
            $product_count = $category->getValue('product_count');
            $children_count = count($category->getValue('children'));
            $is_active = isset($category_tree[0]) && $category_tree[0]->getValue('id') == $category->getValue('id');
            if ($product_count):
                ?>
                <li class="<?= $is_active ? 'is-active' : '' ?>">
                    <a href="<?= $category->getUrl() ?>"><?= $category->getValue($label_name) ?> <span
                            class="number-of-products"><?= $category->getValue('product_count') ?></span></a>

                    <?php if ($children_count):
                        $children = $category->getValue('children');
                        ?>
                        <ul class="<?= $is_active ? 'is-active' : '' ?>">
                            <?php foreach ($children as $child):
                                $product_count = $child->getValue('product_count');
                                $subchild_count = count($child->getValue('children'));
                                $is_active = isset($category_tree[1]) && $category_tree[1]->getValue('id') == $child->getValue('id');
                                if ($product_count): ?>
                                    <li class="<?= $is_active ? 'is-active' : '' ?>">
                                        <a href="<?= $child->getUrl() ?>"><?= $child->getValue($label_name) ?> <span
                                                class="number-of-products"><?= $child->getValue('product_count') ?></span></a>

                                        <?php if ($subchild_count):
                                            $subchildren = $child->getValue('children');
                                            ?>
                                            <ul class="<?= $is_active ? 'is-active' : '' ?>">
                                                <?php foreach ($subchildren as $subchild):
                                                    $product_count = $subchild->getValue('product_count');
                                                    $is_active = isset($category_tree[2]) && $category_tree[2]->getValue('id') == $subchild->getValue('id');
                                                    if ($product_count):
                                                        ?>
                                                        <li class="<?= $is_active ? 'is-active' : '' ?>">
                                                            <a href="<?= $subchild->getUrl() ?>"><?= $subchild->getValue($label_name) ?>
                                                                <span
                                                                    class="number-of-products"><?= $product_count ?></span>
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>

                        </ul>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
<span class="horizontal-rule"></span>
<div class="badges">
    <img src="<?= \rex_url::base('resources/img/siegel_rasenfix_de.jpg') ?>" alt=""/>
</div>