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

$promotion = $this->getVar('promotion');

if (!is_object($promotion)) {
    return;
}

?>
<div class="callout promotion-item secondary"><?= $promotion->getValue('name', true) ?></div>