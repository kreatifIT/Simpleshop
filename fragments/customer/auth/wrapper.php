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

    <?php
    $this->setVar('class', 'large-6 columns margin-bottom');
    echo $this->subfragment('customer/auth/login.php');
    ?>

    <?php
    $this->setVar('class', 'large-6 columns margin-bottom');
    echo $this->subfragment('customer/auth/registration.php');
    ?>

</div>