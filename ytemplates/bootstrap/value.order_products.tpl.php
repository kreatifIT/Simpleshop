<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 18.03.19
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

?>
<div class="form-group">
    <label><?= $this->getLabel() ?></label>

    <div id="order-product-container">
        <?php

        $fragment = new rex_fragment();
        $fragment->setVar('Order', $Order);
        echo $fragment->parse('simpleshop/backend/order_products.php');

        ?>
    </div>
</div>
