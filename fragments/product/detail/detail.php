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

$lang_id  = \rex_clang::getCurrentId();
$product  = $this->getVar('product');
$cat_path = $this->getVar('cat_path');
$badge    = $product->getValue('badge');
$price    = $product->getPrice();
$tax      = Tax::get($product->getValue('tax'))->getValue('tax');
$gallery  = strlen($product->getValue('gallery')) ? explode(',', $product->getValue('gallery')) : [];
$image    = $product->getValue('image');
$variants = $product->getVariants();

if (strlen($image) == 0 && isset($gallery[0]))
{
    $image = $gallery[0];
}

?>
<div class="<?= $this->getVar('class') ?>">

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
                <?php // TODO: angebote
                ?>
                <div class="ribbon"><span>Angebot</span></div>
                <a href="<?= Utils::getImageTag($image, 'fbox', [], function ($params) {
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
                            <?php foreach ($variants['color']->variants as $variant):
                                $amount = $variant->getValue('amount');
                                $surcharge = $variant->getValue('surcharge');
                                ?>
                                <option value="<?= $variant->getValue('id') ?>" <?php if ($amount <= 0)
                                {
                                    echo 'disabled';
                                } ?>>
                                    <?= $variant->getValue('name_' . $lang_id) ?>
                                    <?php
                                    if ($amount <= 0)
                                    {
                                        echo '- nicht verfügbar';
                                    }
                                    else if ($surcharge > 0)
                                    {
                                        echo "(+" . format_price($surcharge) . " &euro;)";
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

                        <?php foreach ($variants['packaging']->variants as $variant): ?>
                            <a>
                                <div class="small">
                                    <img src="<?= \rex_url::base('resources/img/seed.svg') ?>" alt=""/>
                                </div>
                                <span class="weight"><?= $variant->getValue('name_' . $lang_id) ?></span>
                            </a>
                        <?php endforeach; ?>

<!--                        <a href="#">-->
<!--                            <div class="medium">-->
<!--                                <img src="--><?//= \rex_url::base('resources/img/seed.svg') ?><!--" alt=""/>-->
<!--                            </div>-->
<!--                            <span class="weight">5 Kg</span>-->
<!--                        </a>-->
                        <a href="#">
                            <div class="large">
                                <img src="<?= \rex_url::base('resources/img/seed.svg') ?>" alt=""/>
                            </div>
                            <span class="weight">10 Kg</span>
                        </a>
                        <a href="#">
                            <div class="xlarge">
                                <img src="<?= \rex_url::base('resources/img/seed.svg') ?>" alt=""/>
                            </div>
                            <span class="weight">20 Kg</span>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="product-price price-large">
                    <?php // TODO: angebote ?>
                    <span class="price-was">€ 177,00</span>
                    <span class="price">&euro; <?= $price ?></span>
                    <span class="vat">zzgl. <?= $tax ?>% MwSt.</span>
                </div>
                <div class="checkout">
                    <div class="amount-increment clearfix">
                        <button class="button minus">-</button>
                        <input type="text" value="1" readonly>
                        <button class="button plus">+</button>
                    </div>
                    <button class="add-to-cart">
                        <i class="fa fa-cart-plus" aria-hidden="true"></i>
                        <span>In den Warenkorb</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <span class="horizontal-rule"></span>
    <!-- Product description -->
    <div class="row column margin-top">
        <h2>Mit Rasenfix Qualitätsversprechen</h2>
        <div class="clearfix">
            <div class="quality-badge"
                 style="width: 40px; height: 40px; float: left; margin-right: 20px; margin-bottom: 20px;">
                <img src="<?= \rex_url::base('resources/img/rasenfix-quality.svg') ?>" alt=""/>
            </div>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab architecto beatae, culpa laboriosam
                laudantium officia quod veniam vitae!
            </p>
        </div>
        <h2>Beschreibung</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab beatae consectetur harum illo magnam maiores
            repellendus sint totam? Cumque eaque exercitationem hic molestias necessitatibus neque nulla qui, ut
            vel. Dolor nam qui quo similique.</p>
        <h2>Anwendung von Lorem ipsum dolor sit.</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Commodi error, incidunt modi obcaecati officiis
            praesentium quisquam veritatis? Excepturi ipsam perspiciatis qui sapiente veniam?
        </p>
        <ul>
            <li>Lorem ipsum dolor sit amet, consectetur.</li>
            <li>Accusamus dolores id tenetur? Iusto, voluptate!</li>
            <li>Dolorem eveniet incidunt nostrum reprehenderit soluta!</li>
            <li>Culpa deleniti nihil obcaecati sequi ullam!</li>
        </ul>
    </div>
    <span class="horizontal-rule"></span>
    <!-- Similar Products -->
    <div class="shop-products-grid row small-up-1 medium-up-2 large-up-3 margin-large-bottom">
        <?php
        for ($i = 0; $i < 3; $i++)
        {
            echo 'REX_TEMPLATE[38]';
        }
        ?>
    </div>
</div>

