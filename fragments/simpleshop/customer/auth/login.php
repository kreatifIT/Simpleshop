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
    <div id="login">
        <h3>###label.login###</h3>
        <p>###shop.login_text###</p>
        <?php if (count($login_errors)): ?>
            <?php foreach ($login_errors as $error): ?>
                <p class="error red"><?= $error ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
        <form action="" method="post">
            <input type="text" name="email" placeholder="###label.email###" value="<?= rex_post('email', 'string'); ?>">
            <input type="password" name="password" placeholder="###label.password###" value="">
            <span class="lost-password-button button button-gray expanded">###label.password_forgotten###</span>
            <button type="submit" class="button expanded" name="action" value="login">###action.login###</button>
        </form>
    </div>

    <div id="lost-password" style="display: none;">
        <h3>###label.password_forgotten###</h3>
        <p>###label.pwd_reset_msg###</p>
        <form action="" method="post">
            <input type="text" name="email" placeholder="###label.email###" value="<?= rex_post('email', 'string'); ?>">
            <span class="back-to-login-button button button-gray expanded">###action.back_to_login###</span>
            <button type="submit" class="button expanded" name="action" value="login">###action.send###</button>
        </form>
        <!-- popup -->
        <div id="login-modal" class="login-modal" style="display: none;">
            <div class="row shop-modal-content">###notif.password_reset_msg###</div>
            <span class="button expanded">###general.ok###</span>
        </div>
    </div>
</div>
