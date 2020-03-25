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
    <?php if ($config['has_remove_button']): ?>
        <th class="action">&nbsp;</th>
    <?php endif; ?>
    <?php if ($config['has_image']): ?>
        <th class="img">###label.preview###</th>
    <?php endif; ?>
    <th class="name">###label.product###</th>
    <th class="price"><?= $use_tax_prices ? '###label.single_price###' : '###label.single_price_no_vat###' ?></th>
    <th class="amount">###label.amount###</th>
    <th class="total">###label.total###</th>
</tr>