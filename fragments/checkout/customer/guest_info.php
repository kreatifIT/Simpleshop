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

?>
<div class="<?= $this->getVar('class') ?>">
    <div>
        <h3><?= $this->i18n('label.buy_as_guest') ?></h3>
        <p><?= $this->i18n('label.buy_as_guest_text') ?></p>
        <a href="#" class="button"><?= $this->i18n('action.buy_as_guest'); ?></a>
    </div>
</div>


