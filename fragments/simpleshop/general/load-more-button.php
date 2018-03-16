<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 16.03.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$uid   = mt_rand(0, 9999);
$pid   = $this->getVar('page_id');
$fid   = $this->getVar('fragment_id');
$label = $this->getVar('label', '###label.load_more###');

if ($pid == 0) {
    return;
}

?>
<div class="load-more" id="load-more-container-<?= $uid ?>">
    <div class="text-center">
        <a href="<?= rex_getUrl(null, null, array_merge($_GET, ['p' => $pid])) ?>" class="button margin-top margin-large-bottom" onclick="return Simpleshop.loadMore(this, '#load-more-container-<?= $uid ?>', '<?= $fid ?>')">
            <?= $label ?>
        </a>
    </div>
</div>
