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

$login_errors = $this->getVar('login_errors');

?>
<div class="<?= $this->getVar('class') ?>">
    <div>
        <h3><?= $this->i18n('label.login') ?></h3>
        <p><?= $this->i18n('label.login_text') ?></p>
        <?php if (count($login_errors)): ?>
            <?php foreach ($login_errors as $error): ?>
                <p class="error red"><?= $error ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
        <form action="" method="post">
            <input type="text" name="email" placeholder="<?= $this->i18n('label.email') ?>" value="<?= rex_post('email', 'string'); ?>">
            <input type="password" name="password" placeholder="<?= $this->i18n('label.password') ?>" value="" autocomplete="off">
            <a href="#" class="lost-password"><?= $this->i18n('label.password_forgotten'); ?></a>
            <button type="submit" class="button" name="action" value="login"><?= $this->i18n('action.login'); ?></button>
        </form>
    </div>
</div>
