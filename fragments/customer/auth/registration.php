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
        <h3><?= $this->i18n('label.new_customer') ?></h3>
        <p><?= $this->i18n('label.new_customer_text') ?></p>
        <form action="" method="post">
            <input type="text" placeholder="<?= $this->i18n('label.firstname'); ?>">
            <input type="text" placeholder="<?= $this->i18n('label.lastname'); ?>">
            <input type="text" placeholder="<?= $this->i18n('label.email_address'); ?>">
            <button type="submit" class="button"><?= $this->i18n('action.register'); ?></button>
        </form>
    </div>
</div>
