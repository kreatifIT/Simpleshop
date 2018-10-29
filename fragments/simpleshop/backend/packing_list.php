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


$Order    = $this->getVar('order');
$Shipping = $Order->getValue('shipping');
$barCode  = $Order->getShippingKey();
$products = $Order->getProducts(false);
$SAddress = $Order->getShippingAddress();


if (strlen($barCode)) {
    $Code = new \Barcode($barCode, 4);

    ob_start();
    imagepng($Code->image());
    $image = ob_get_clean();
    echo '<img src="data:image/png;base64,' . base64_encode($image) . '">';
}
exit;

$Addon     = \rex_addon::get('simpleshop');
$css_files = \rex_view::getCssFiles();

$addr_data = $SAddress->getData();

foreach ($css_files['all'] as $index => $file) {
    if (preg_match('!\/be_style\/plugins\/!', $file)) {
        unset($css_files['all'][$index]);
    }
}

$fragment = new \rex_fragment();
$fragment->setVar('pageTitle', \rex_be_controller::getPageTitle());
$fragment->setVar('cssFiles', $css_files);
$fragment->setVar('jsFiles', \rex_view::getJsFiles());
$fragment->setVar('jsProperties', json_encode(\rex_view::getJsProperties()), false);
$fragment->setVar('favicon', \rex_view::getFavicon());
$fragment->setVar('pageHeader', \rex_extension::registerPoint(new \rex_extension_point('PAGE_HEADER', '')), false);
$fragment->setVar('bodyAttr', ' class="packing-list-page" style="background-color:#666;"', false);
echo $fragment->parse('core/top.php');

?>
<h3><?= $Addon->i18n('label.packing_list_title'); ?> #<?= $Order->getValue('id') ?></h3>

<div class="row">
    <div class="col-xs-6">
        <div class="panel panel-default address">
            <div class="panel-heading"><?= $Addon->i18n('label.return_address') ?></div>
            <div class="panel-body">
                <?= Wildcard::get('company.legal_name') ?><br/>
                <?= Wildcard::get('company.street') ?><br/>
                <?= Wildcard::get('company.location') ?><br/>
                <?= Wildcard::get('company.postal') ?><br/>
                <?= Wildcard::get('company.region') ?> (<?= Wildcard::get('company.country') ?>)<br/>
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="panel panel-default address">
            <div class="panel-heading"><?= $Addon->i18n('label.shipping_address') ?></div>
            <div class="panel-body">
                <?php foreach ($addr_data as $field => $value): ?>
                    <?php if (strlen($value) != '' && !in_array($field, ['id', 'type', 'lastname', 'customer_id', 'createdate', 'updatedate', 'salutation', 'fiscal_code', 'vat_num', 'phone'])): ?>
                        <?= $value ?> <?= $field == 'firstname' ? $addr_data['lastname'] : '' ?><br/>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default product-list">
    <div class="panel-heading"><?= $Addon->i18n('label.product_list') ?></div>
    <table class="table table-bordered table-condensed">
        <tr>
            <th>Nr</th>
            <th>Code</th>
            <th><?= $Addon->i18n('label.name') ?></th>
            <th><?= $Addon->i18n('label.weight') ?></th>
            <th><?= $Addon->i18n('label.amount') ?></th>
        </tr>
        <?php foreach ($products as $index => $_product):
            $product = $_product->getValue('data');
            ?>
            <tr>
                <td class="xxs-small text-center"><?= $index + 1 ?></td>
                <td class="small"><?= $product->getValue('code') ?></td>
                <td>
                    <div class="description"><?= $product->getValue('name', true) ?></div>
                    <?php if ($product->getValue('category_id')): ?>
                        <div class="description"><em><?= $Addon->i18n('label.category') ?>: <?= Category::get($product->getValue('category_id'))
                                    ->getValue('name', true) ?></em></div>
                    <?php endif; ?>
                    <?php if ($product->getValue('width') || $product->getValue('length') || $product->getValue('height')): ?>
                        <div class="description"><?= (int)($product->getValue('width') ?: 0.1) / 10 ?>cm x <?= (int)($product->getValue('length') ?: 0.1) / 10 ?>cm x <?= (int)($product->getValue('height') ?: 0.1) / 10 ?>cm</div>
                    <?php endif; ?>
                </td>
                <td class="xs-small text-center"><?= $product->getValue('weight') ? $product->getValue('weight') . 'g' : '-' ?></td>
                <td class="xs-small text-center"><?= $product->getValue('cart_quantity') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>