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

$template = $this->getVar('template');

$curId    = \rex_article::getCurrentId();
$Category = \rex_category::getCurrent();
$articles = $Category->getArticles(true);

?>
<div class="customer-area margin-top margin-large-bottom">
    <div class="row">
        <?php if (count($articles) > 1): ?>
            <div class="column large-3 sidebar margin-bottom">
                <ul class="no-bullet">
                    <?php foreach ($articles as $article): ?>
                        <li <?= $curId == $article->getId() ? 'class="active"' : '' ?>>
                            <a href="<?= $article->getUrl() ?>"><?= $article->getName() ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="column container <?= count($articles) > 1 ? 'large-9' : '' ?>">
            <?= $this->subfragment('simpleshop/customer/customer_area/' . $template); ?>
        </div>
    </div>
</div>