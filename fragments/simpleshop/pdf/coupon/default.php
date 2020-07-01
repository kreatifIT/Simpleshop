<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 01.07.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$coupon = $this->getVar('coupon');

?>
<div class="coupon-wrapper">

    <h3><?= $coupon->getCode() ?></h3>

</div>
