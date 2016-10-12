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

$name   = $this->getVar('name');
$fields = $this->getVar('fields');
$type   = $this->getVar('type');

?>
<tr class="type-<?= $type ?>">
    <td>
    <span
        style="white-space:nowrap;">- <?= implode('<br/></span><span style="white-space:nowrap;">- ', $name) ?></span>
    </td>
    <?php foreach ($fields as $field): ?>
        <td><?= $field ?></td>
    <?php endforeach; ?>
</tr>



