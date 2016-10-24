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

$email = $this->getVar('email');
$password = $this->getVar('password');

?>
<p>###shop.email.registration_text###</p>

<p>
    ###label.email###: <?= $email ?><br/>
    ###label.password###: <?= $password ?>
</p>
