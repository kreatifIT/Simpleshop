<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 12.03.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$shipping = $this->getVar('shipping');

?>
<div class="medium-6 column margin-bottom">
    <h3 class="heading small">###label.shipping_method###</h3>
    <?= $shipping->getName() ?>
</div>
