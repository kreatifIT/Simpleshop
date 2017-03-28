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

$config = $this->getVar('config', []);
$styles = array_merge([
    'tr' => '',
    'th' => '',
], $config['email_tpl_styles']);

?>
<tr class="cart-header" <?= $styles['tr'] ? 'style="'. $styles['tr'] .'"' : '' ?>>
    <?php if ($config['has_image']): ?>
        <th <?= $styles['th'] ? 'style="'. $styles['th'] .'"' : '' ?>>###label.preview###</th>
    <?php endif; ?>
    <th <?= $styles['th'] ? 'style="'. $styles['th'] .'"' : '' ?>>###label.product###</th>
    <th <?= $styles['th'] ? 'style="'. $styles['th'] .'"' : '' ?>>###label.single_price###</th>
    <th <?= $styles['th'] ? 'style="'. $styles['th'] .'"' : '' ?>>###label.amount###</th>
    <th <?= $styles['th'] ? 'style="'. $styles['th'] .'"' : '' ?>>###label.total###</th>
    <?php if ($config['has_remove_button']): ?>
        <th <?= $styles['th'] ? 'style="'. $styles['th'] .'"' : '' ?>>&nbsp;</th>
    <?php endif; ?>
</tr>