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

$password = $this->getVar('password');
$User     = $this->getVar('User');

?>
<p>###shop.email.password_reset_text###</p>

<p>
    ###label.email###: <?= $User->getValue('email') ?><br/>
    ###label.password###: <?= $password ?>
</p>
