<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 22.08.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;

$Address    = $this->getVar('Address');
$canAddItem = $this->getVar('canAddItem', true);

?>
<li class="grid-x grid-margin-x margin-small-bottom">
    <div class="cell medium-6">
        <?= $Address->getName() ?><br/>
        <?= $Address->getValue('street') ?>
        <?= $Address->valueIsset('street_additional') ? " - {$Address->getValue('street_additional')}" : '' ?><br/>
        <?= $Address->getValue('postal') ?> <?= $Address->getValue('location') ?><br/>
    </div>
    <div class="cell medium-6">
        <?php if ($canAddItem): ?>
            <a href="<?= rex_getUrl(null, null, [
                'action'  => 'edit',
                'ctrl'    => 'addresses.detail',
                'data-id' => $Address->getId(),
            ]) ?>">###action.edit###</a>
            <br/>
        <?php endif; ?>
    </div>
</li>
