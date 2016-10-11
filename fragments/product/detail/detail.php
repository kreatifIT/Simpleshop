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

use Leafo\ScssPhp\Util;

$lang_id     = \rex_clang::getCurrentId();
$product     = $this->getVar('product');
$cat_path    = $this->getVar('cat_path');
$badge       = $product->getValue('badge');
$price       = $product->getValue('price');
$offer_price = $product->getValue('reduced_price');
$tax         = Tax::get($product->getValue('tax'))->getValue('tax');
$gallery     = strlen($product->getValue('gallery')) ? explode(',', $product->getValue('gallery')) : [];
$image       = $product->getValue('image');
$variants    = $product->getFeatureVariants();
$description = $product->getValue('description_' . $lang_id);
$application = $product->getValue('application_' . $lang_id);

$before_content = $this->getVar('before_content');
$after_content  = $this->getVar('after_content');

if (strlen($image) == 0 && isset($gallery[0]))
{
    $image = $gallery[0];
}

?>
<?php if ($this->getVar('has_infobar') && count($cat_path)): ?>
    <div class="shop-products-info-bar">
        <div class="breadcrumbs">
            <ul class="rex-breadcrumb">
                <?php foreach ($cat_path as $index => $path):
                    if (isset ($path['label'])):
                        ?>
                        <li class="rex-lvl<?= $index + 1 ?>">
                            <?php if (isset($path['url'])): ?><a href="<?= $path['url'] ?>"><?php else: ?>
                                <span><?php endif; ?>
                                    <?= $path['label'] ?>
                                <?php if (isset($path['url'])): ?></a><?php else: ?></span><?php endif; ?>
                        </li>
                        <?php
                    endif;
                endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="large-6 columns">
        <div class="product-image-detail">
            <?php if ($offer_price > 0): ?>
                <div class="ribbon"><span><?= $this->i18n('label.offer'); ?></span></div>
            <?php endif; ?>
            <a href="<?= Utils::getImageTag($image, 'fbox', [], function ($params)
            {
                return $params['attributes']['src'];
            }) ?>" class="fbox"><?= Utils::getImageTag($image, 'product-detail-main') ?></a>
        </div>
    </div>
    <div class="large-6 columns">
        <div class="product-info-panel">
            <?php if (strlen($badge)) : ?>
                <div class="badge">
                    <?= Utils::getImageTag($badge, '') ?>
                </div>
            <?php endif; ?>

            <?php if (isset($variants['color'])): ?>
                <h3><?= $variants['color']->getValue('name_' . $lang_id) ?></h3>
                <div class="select margin-small-bottom">
                    <select name="color" id="color">
                        <?php foreach ($variants['color']->values as $feature_value):
                            $amount = $feature_value->getValue('min_amount');
                            ?>
                            <option value="<?= $feature_value->getValue('id') ?>" <?php if ($amount <= 0)
                            {
                                echo 'disabled';
                            } ?> data-packaging-ids="">
                                <?= $feature_value->getValue('name_' . $lang_id) ?>
                                <?php
                                if ($amount <= 0)
                                {
                                    echo '- nicht verfügbar';
                                }
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if (isset($variants['packaging'])): ?>
                <h3><?= $variants['packaging']->getValue('name_' . $lang_id) ?></h3>
                <div class="packaging clearfix">

                    <?php foreach ($variants['packaging']->values as $variant):
                        $icon = $variant->getValue('icon');
                    ?>
                        <a data-price="" data-reduced-price="" data-img-src="">
                            <?php if (strlen($icon)): ?>
                            <div class="small">
                                <?= Utils::getImageTag($icon, '') ?>
                            </div>
                            <?php endif; ?>
                            <span class="weight"><?= $variant->getValue('name_' . $lang_id) ?></span>
                        </a>
                    <?php endforeach; ?>

                    <!--                        <a href="#">-->
                    <!--                            <div class="medium">-->
                    <!--                                <img src="-->
                    <? //= \rex_url::base('resources/img/seed.svg') ?><!--" alt=""/>-->
                    <!--                            </div>-->
                    <!--                            <span class="weight">5 Kg</span>-->
                    <!--                        </a>-->
<!--                    <a href="#" class="active">-->
<!--                        <div class="large">-->
<!--                            <img src="--><?//= \rex_url::base('resources/img/seed.svg') ?><!--" alt=""/>-->
<!--                        </div>-->
<!--                        <span class="weight">10 Kg</span>-->
<!--                    </a>-->
<!--                    <a href="#">-->
<!--                        <div class="xlarge">-->
<!--                            <img src="--><?//= \rex_url::base('resources/img/seed.svg') ?><!--" alt=""/>-->
<!--                        </div>-->
<!--                        <span class="weight">20 Kg</span>-->
<!--                    </a>-->
                </div>
            <?php endif; ?>

            <div class="product-price price-large">
                <?php if ($offer_price > 0): ?>
                    <span class="price-was">&euro; <?= format_price($price) ?></span>
                <?php endif; ?>
                <span class="price">&euro; <?= format_price($offer_price > 0 ? $offer_price : $price) ?></span>
                <span class="vat"><?= strtr($this->i18n('label.vat_in_addition'), ['{{tax}}' => $tax]); ?></span>
            </div>
            <div class="checkout">
                <?php
                $this->setVar('button-cart-counter', 1);
                $this->setVar('has_quantity_control', TRUE);
                $this->setVar('has_add_to_cart_button', TRUE);
                echo $this->subfragment('product/general/cart/button.php');
                ?>
            </div>
        </div>
    </div>
</div>

<span class="horizontal-rule"></span>

<div class="row column margin-top">

    <?php if (strlen($before_content))
    {
        echo $before_content;
    } ?>

    <?php if (strlen($description)): ?>
        <h2><?= $this->i18n('label.description'); ?></h2>
        <?= $description ?>
    <?php endif; ?>

    <?php if (strlen($application)): ?>
        <h2><?= strtr($this->i18n('label.application'), ['{{product_name}}' => $product->getValue('name_' . $lang_id)]); ?></h2>
        <?= $application ?>
    <?php endif; ?>
</div>

