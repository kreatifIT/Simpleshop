<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 13.08.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$title = $this->getVar('title');
$text  = $this->getVar('text');

if (!strlen($title) && !strlen($text)) {
    return;
}

?>
<div class="margin-small-bottom">
    <?php if (strlen($title)): ?>
        <h2 class="<?= strlen($text) ? 'margin-bottom' : '' ?>"><?= $title ?></h2>
    <?php endif; ?>
    <?= $text ?>
</div>
