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

use Sprog\Wildcard;

$lable_name = sprogfield('name');
$product = $this->getVar('product');
$cat_path = $this->getVar('cat_path');
$badge = $product->getValue('badge');
$price = $product->getPrice(TRUE, FALSE);
$offer_price = $product->getValue('reduced_price');
$tax = Tax::get($product->getValue('tax'))->getValue('tax');
$gallery = strlen($product->getValue('gallery')) ? explode(',', $product->getValue('gallery')) : [];
$image = $product->getValue('image');
$pictures = strlen($product->getValue('pictures')) ? explode(',', $product->getValue('pictures')) : [];
$variants = $product->getFeatureVariants();
$description = $product->getValue(sprogfield('description'));
$application = $product->getValue(sprogfield('application'));
$delivery_time = $product->getValue(sprogfield('delivery_time'));
//pr($variants['mapping']);
$default_color_id = 0;
$default_packaging_id = 0;

$before_content = $this->getVar('before_content');
$after_content = $this->getVar('after_content');

if (strlen($image) == 0 && isset($gallery[0])) {
    $image = $gallery[0];
}

?>
<?php if ($this->getVar('has_infobar') && count($cat_path)): ?>
    <div class="shop-products-info-bar clearfix">
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
                <div class="ribbon"><span>###label.offer###</span></div>
            <?php endif; ?>
            <a href="<?= Utils::getImageTag($image, 'fbox', [], function ($params) {
                return $params['attributes']['src'];
            }) ?>" class="fbox" rel="product-images"><?= Utils::getImageTag($image, 'product-detail-main') ?></a>
        </div>
        <?php if (count($pictures)): ?>
        <div class="row product-image-detail-carousel">
            <?php foreach ($pictures as $picture): ?>
                <div class="column">
                    <div class="product-image">
                        <a href="<?= Utils::getImageTag($picture, 'fbox', [], function ($params) {
                            return $params['attributes']['src'];
                        }) ?>" class="fbox" rel="product-images">
                            <?= Utils::getImageTag($picture, 'cart-list-element-main') ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="large-6 columns">
        <!-- Popup -->
        <?php
        $this->subfragment('simpleshop/product/general/cart/popup-wrapper.php');
        ?>
        <div class="product-info-panel">
            <?php if (strlen($badge)) : ?>
                <div class="badge">
                    <?= Utils::getImageTag($badge, '') ?>
                </div>
            <?php endif; ?>

            <?php if (isset($variants['features']['color'])): ?>
                <h3><?= $variants['features']['color']->getValue($lable_name) ?></h3>
                <div class="variant-select-container">
                    <select name="variant" class="variant-select">
                        <?php foreach ($variants['features']['color']->values as $feature_value):
                            $value_id = $feature_value->getValue('id');
                            $amount = $variants['mapping'][$value_id]['min_amount'];
                            if (!$default_color_id) {
                                $default_color_id = $value_id;
                            }
                            $icon = \Kreatif\Resource::getImageTag($feature_value->getValue('icon'), '', [], function ($params) {
                                return $params['attributes']['src'];
                            });
                            $image = $icon ?: \rex_url::base('resources/img/logo-rasenfix.svg');
                            ?>
                            <option
                                value="<?= $value_id ?>"
                                <?php if ($amount <= 0) echo 'disabled'; ?>
                                data-variant-ids="<?= implode(',', array_keys($variants['mapping'][$value_id]['variants'])) ?>"
                                data-image="<?= $image ?>"
                            >
                                <?= $feature_value->getValue($lable_name) ?>
                                <?php
                                if ($amount <= 0) {
                                    echo '- nicht verfügbar';
                                }
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if (isset($variants['features']['packaging'])): ?>
                <h3><?= $variants['features']['packaging']->getValue($lable_name) ?></h3>
                <div class="packaging clearfix">

                    <?php
                    $selected = false;
                    foreach ($variants['features']['packaging']->values as $feature_value):
                        $icon = $feature_value->getValue('icon');
                        $value_id = $feature_value->getValue('id');
                        $amount = $variants['mapping'][$value_id]['min_amount'];
                        if (!$default_packaging_id) {
                            $default_packaging_id = $value_id;
                        }
                        $weight = $feature_value->getValue('key');
                        $icon_sizes = \Kreatif\Project\Settings::$PACKAGING_SIZES;
                        $icon_size = $icon_sizes[0];
                        foreach ($icon_sizes as $key => $value) {
                            if ($weight <= $value) {
                                $icon_size = $key;
                                break;
                            }
                        }
                        ?>
                        <button data-value="<?= $value_id ?>" class="
                        <?php
                        if ($amount <= 0) {
                            echo 'disabled';
                        } elseif (!$selected) {
                            echo 'selected';
                            $selected = true;
                        }
                        ?>">
                            <?php if (strlen($icon)): ?>
                                <div class="<?= $icon_size ?>">
                                    <?= file_get_contents(\rex_path::base('resources/img/' . $icon)) ?>
                                </div>
                            <?php endif; ?>
                            <span class="weight"><?= $feature_value->getValue($lable_name) ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="product-price price-large">
                <span
                    class="price-was <?php if ($offer_price <= 0) echo 'hidden' ?>">&euro; <?= format_price($price) ?></span>
                <span
                    class="price">&euro; <?= format_price($offer_price > 0 ? $product->getPrice(true) : $price) ?></span>
                <span class="vat"><?= strtr(Wildcard::get('shop.vat_included'), ['{{tax}}' => $tax]); ?></span>
                <?php if (strlen($delivery_time)): ?>
                    <div class="delivery-times">###shop.delivery_time###: <?= $delivery_time ?></div><?php endif; ?>
            </div>
            <div class="checkout">
                <?php
                if (count($variants['mapping'])) {
                    $variant_key = array_keys($variants['variants']);
                } else {
                    $variant_key = [''];
                }
                $this->setVar('button-cart-counter', 1);
                $this->setVar('has_quantity_control', TRUE);
                $this->setVar('has_add_to_cart_button', TRUE);
                $this->setVar('is_disabled', $product->getValue('amount') <= 0);
                $this->setVar('product_key', $product->getValue('id') . '|' . $variant_key[0]);
                echo $this->subfragment('simpleshop/product/general/cart/button.php');
                ?>
            </div>
        </div>
    </div>
</div>

<span class="horizontal-rule"></span>

<div class="row column margin-top">

    <?php if (strlen($before_content)) {
        echo $before_content;
    } ?>

    <?php if (strlen($description)): ?>
        <h2>###label.description###</h2>
        <?= $description ?>
    <?php endif; ?>

    <?php if (strlen($application)): ?>
        <h2><?= strtr(Wildcard::get('label.application'), ['{{product_name}}' => $product->getValue($lable_name)]); ?></h2>
        <?= $application ?>
    <?php endif; ?>
</div>
<?php
$variant_data = [];

foreach ($variants['variants'] as $key => $variant) {
    $variant_data[$key] = $variant->getData();
    $variant_data[$key]['price_formated'] = format_price($variant->getPrice(TRUE));
    $variant_data[$key]['orig_price_formated'] = $variant_data[$key]['reduced_price'] ? format_price($variant->getPrice(TRUE, FALSE)) : NULL;
    $variant_data[$key]['image_big_full'] = Utils::getImageTag($variant_data[$key]['image'], 'fbox', [], function ($params) {
        return $params['attributes']['src'];
    });
    $variant_data[$key]['image_full'] = Utils::getImageTag($variant_data[$key]['image'], 'product-detail-main', [], function ($params) {
        return $params['attributes']['src'];
    });
}
?>
<script>
    var FeatureValueMapping = <?= json_encode($variants['mapping']) ?>;
    var Variants = <?= json_encode($variant_data) ?>;
</script>
