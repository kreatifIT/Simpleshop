<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 11.04.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;

$Object   = \rex::getProperty('url_object');
$variants = $this->getVar('variants');
$selector = $this->getVar('container_selector', '.default-variant-selector');
$vkey     = $Object ? $Object->getValue('variant_key') : '';

?>
<select onchange="Simpleshop.toggleVariant(this, '<?= $selector ?>')">
    <?php foreach ($variants['variants'] as $feature_ids => $Variant):
        $features = FeatureValue::getByVariantKey($feature_ids, true);
        ?>
        <option value="<?= $Variant->getUrl(['vkey' => $Variant->getValue('variant_key')]) ?>" <?= $vkey == $Variant->getValue('variant_key') ? 'selected="selected"' : '' ?>><?= implode(' + ', $features) ?></option>
    <?php endforeach; ?>
</select>