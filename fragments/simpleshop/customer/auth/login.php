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
            <a href="#" class="lost-password">###label.password_forgotten###</a>
            <button type="submit" class="button" name="action" value="login">###action.login###</button>
        </form>
    </div>
</div>
