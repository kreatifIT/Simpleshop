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

$config = $this->getVar('cart_config', FragmentConfig::getValue('cart'));
$styles = FragmentConfig::getValue('styles');

?>
<tr class="cart-header" <?= $styles['tr'] ?>>
    <?php if ($config['has_image']): ?>
        <th <?= $styles['prod-th'] ?>>###label.preview###</th>
    <?php endif; ?>
    <th <?= $styles['prod-th'] ?>>###label.product###</th>
    <th <?= $styles['prod-th'] ?>>###label.amount###</th>
    <th <?= $styles['prod-th'] ?>>###label.single_price###</th>
    <th <?= $styles['prod-th'] ?>>###label.total###</th>
    <?php if ($config['has_remove_button']): ?>
        <th <?= $styles['prod-th'] ?>>&nbsp;</th>
    <?php endif; ?>
</tr>