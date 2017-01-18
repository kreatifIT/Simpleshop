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

$Order     = $this->getVar('order');
$SAddress  = $this->getVar('saddress');
$products  = $this->getVar('products');
$Addon     = \rex_addon::get('simpleshop');
$addr_data = $SAddress->getData();
$css_files = \rex_view::getCssFiles();

foreach ($css_files['all'] as $index => $file)
{
    if (preg_match('!\/be_style\/plugins\/!', $file))
    {
        unset($css_files['all'][$index]);
    }
}

$fragment = new \rex_fragment();
$fragment->setVar('pageTitle', \rex_be_controller::getPageTitle());
$fragment->setVar('cssFiles', $css_files);
$fragment->setVar('jsFiles', \rex_view::getJsFiles());
$fragment->setVar('jsProperties', json_encode(\rex_view::getJsProperties()), FALSE);
$fragment->setVar('favicon', \rex_view::getFavicon());
$fragment->setVar('pageHeader', \rex_extension::registerPoint(new \rex_extension_point('PAGE_HEADER', '')), FALSE);
$fragment->setVar('bodyAttr', ' class="packing-list-page" style="background-color:#666;"', FALSE);
echo $fragment->parse('core/top.php');

?>
<h3><?= $Addon->i18n('label.packing_list_title'); ?> #<?= $Order->getValue('id') ?></h3>

<div class="panel panel-default address">
    <div class="panel-heading"><?= $Addon->i18n('label.shipping_address') ?></div>
    <div class="panel-body">
        <?php foreach ($addr_data as $field => $value): ?>
            <?php if (strlen($value) != '' && !in_array($field, ['id', 'type', 'lastname', 'customer_id', 'createdate', 'updatedate', 'salutation'])): ?>
                <?= $value ?> <?= $field == 'firstname' ? $addr_data['lastname'] : '' ?><br/>
            <?php endif; ?>
        <?php endforeach; ?>
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
                    <div class="description"><?= $product->getValue(sprogfield('name')) ?></div>
                    <?php if ($product->getValue('category_id')): ?>
                        <div class="description"><em><?= $Addon->i18n('label.category') ?>: <?= Category::get($product->getValue('category_id'))->getValue(sprogfield('name')) ?></em></div>
                    <?php endif; ?>
                    <?php if ($product->getValue('width') || $product->getValue('length') || $product->getValue('height')): ?>
                        <div class="description"><?= (int) ($product->getValue('width') ?: 0.1) / 10 ?>cm x <?= (int) ($product->getValue('length') ?: 0.1) / 10 ?>cm x <?= (int) ($product->getValue('height') ?: 0.1) / 10 ?>cm</div>
                    <?php endif; ?>
                </td>
                <td class="xs-small text-center"><?= $product->getValue('weight') ? $product->getValue('weight') / 100 . 'g' : '-' ?></td>
                <td class="xs-small text-center"><?= $product->getValue('cart_quantity') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>