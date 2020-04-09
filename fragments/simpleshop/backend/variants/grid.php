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


$labels = $this->getVar('labels');
$rows   = $this->getVar('rows');

?>
<table class="table table-condensed variants" valign="top">
    <tr>
        <th>&nbsp;</th>
        <th><?= $this->i18n('label.name') ?></th>
        <?php foreach ($labels as $label): ?>
            <th>
                <?= $label['label'] ?>
                <?php if (strlen($label['notice']) && $label['notice'] != 0): ?>
                    <br/>
                    <small class="help-block"><?= $label['notice'] ?></small>
                <?php endif; ?>
            </th>
        <?php endforeach; ?>
    </tr>

    <?php
    foreach ($rows as $row) {
        echo $row;
    }
    ?>
</table>



