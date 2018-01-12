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

$email    = $this->getVar('email');
$addText  = $this->getVar('additional_text', '');
$password = $this->getVar('password');
$url      = $this->getVar('url');
$config   = array_merge([], $this->getVar('config', []));

?>
<p>###shop.email.registration_text###</p>

<p>
    <?php if (strlen($url)): ?>###label.website###: <a href="<?= $url ?>" style="color:<?= $config['primary_color'] ?>"><?= $url ?></a><?php endif; ?><br/>
    ###label.email###: <?= $email ?><br/>
    ###label.password###: <?= $password ?>
</p>

<?php if (strlen($addText)): ?>
    <p><?= $addText ?></p>
<?php endif; ?>
