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

$config         = $this->getVar('cart_config', FragmentConfig::getValue('cart'));
$use_tax_prices = $this->getVar('use_tax_prices', true);

?>
<tr class="cart-header">
    <?php if ($config['has_image']): ?>
        <th>###label.preview###</th>
    <?php endif; ?>
    <th>###label.product###</th>
    <th>###label.amount###</th>
    <th><?= $use_tax_prices ? '###simpleshop.single_price###' : '###simpleshop.single_price_no_vat###' ?></th>
    <th>###label.total###</th>
    <?php if ($config['has_remove_button']): ?>
        <th>&nbsp;</th>
    <?php endif; ?>
</tr>