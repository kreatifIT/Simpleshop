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

$message = $this->getVar('message');

?>
<div class="small-12 columns">
    <div class="description">
        <strong><?= Wildcard::get('label.error') ?></strong><br/>
        <p><?= $message ?></p>
    </div>
</div>