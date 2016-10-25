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

$label_name = sprogfield('name');
$product    = $this->getVar('product');
$sidebar    = $this->getVar('sidebar_content');

?>
<div class="row column margin-top">
    <h1 class="heading"><span><?= $product->getValue($label_name) ?></span></h1>
</div>
<div class="row">
    <div class="shop-sidebar large-3 columns margin-bottom">
        <?= $sidebar ?>
    </div>

    <div class="large-9 columns product-detail">
        <!-- Product Detail -->
        <?php
        $this->setVar('product', $product);
        $this->setVar('has_infobar', TRUE);

        ob_start();
        @$this->subfragment('simpleshop/product/detail/before_content.php');
        $before_content = ob_get_clean();

        $this->setVar('before_content', $before_content, FALSE);
        echo $this->subfragment('simpleshop/product/detail/detail.php');
        ?>

        <span class="horizontal-rule"></span>

        <!-- Similar Products -->
        <?php
        $category_id = $product->getValue('category_id');
        $products    = Product::query()
            ->where('category_id', $category_id)
            ->where('id', $product->getValue('id'), '!=')
            ->orderByRaw('RAND()', '')
            ->limit(0, 3)
            ->find();

        if (count($products)):
            ?>
            <h3>###shop.similar_products###</h3>
            <div class="shop-products-grid row small-up-1 medium-up-2 large-up-3 margin-large-bottom">
                <?php

                foreach ($products as $product)
                {
                    $this->setVar('class', 'info', FALSE);
                    $this->setVar('product', $product);
                    echo $this->subfragment('simpleshop/product/list/element.php');
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

