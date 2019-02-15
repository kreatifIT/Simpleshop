<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 14.02.19
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

$action = rex_get('action', 'string');

$fragment = new \rex_fragment();
$content  = $fragment->parse('simpleshop/backend/list_functions/link_product_rex_categories.php');

$fragment = new \rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('label.' . $action));
$fragment->setVar('body', $content, false);
$sections = $fragment->parse('core/page/section.php');

?>
<div style="margin-top:30px;">
    <form action="<?= \rex_url::currentBackendPage() ?>" method="post">
        <?= $sections ?>
    </form
</div>
