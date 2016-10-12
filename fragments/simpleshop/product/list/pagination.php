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

$total         = $this->getVar('total');
$element_count = $this->getVar('element_count') ? $this->getVar('element_count') : 20;
$paging_params = $this->getVar('paging_params') ? $this->getVar('paging_params') : [];

if (true || $total > $element_count):
    $pagination  = Utils::getPagination($total, $element_count, [], $paging_params);
?>
<div class="row column margin-top margin-bottom">
    <ul class="pagination text-center" aria-label="Pagination">

        <?php foreach ($pagination as $item): ?>
            <li class="<?= $item['class'] ?>">
                <?php if ($item['type'] == 'active'): ?>
                    <?= $item['text'] ?>
                <?php else: ?>
                    <a href="<?= $item['url'] ?>"><?= $item['text'] ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>

    </ul>
</div>
<?php endif; ?>