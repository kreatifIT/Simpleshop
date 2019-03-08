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

$Order = $this->getVar('Order');

?>
<div class="cell margin-bottom">
    <p class="text-center"><?= strtr(Wildcard::get('simpleshop.banktransfer_order_complete_text'), [
            '{{total}}'    => '<strong>&euro;&nbsp;' . format_price($Order->getValue('total')) . '</strong>',
            '{{order_id}}' => $Order->getValue('id'),
        ]); ?></p>
</div>
