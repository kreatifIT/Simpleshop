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

$errors = $this->getVar('registration_errors');

?>
<div class="<?= $this->getVar('class') ?>">
    <div>
        <h3>###label.new_customer###</h3>
        <p>###shop.new_customer_text###</p>
    
        <?php if (count($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <p class="callout alert"><?= $error ?></p>
            <?php endforeach; ?>
        <?php endif; ?>

        <form action="" method="post">
            <input type="text" placeholder="###label.firstname###" name="firstname" value="<?= rex_post('firstname', 'string'); ?>"/>
            <input type="text" placeholder="###label.lastname###" name="lastname" value="<?= rex_post('lastname', 'string'); ?>"/>
            <input type="text" placeholder="###label.email###" name="email" value="<?= rex_post('email', 'string'); ?>"/>
            <button type="submit" class="button expanded" name="action" value="registration">###action.register###</button>
        </form>
    </div>
</div>
