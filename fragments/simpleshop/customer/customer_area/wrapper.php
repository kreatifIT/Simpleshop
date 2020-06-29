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
$cur_area = $this->getVar('area');
$Settings = \rex::getConfig('simpleshop.Settings');
$User     = Customer::getCurrentUser();

\emailobfuscator::whitelistEmail($User->getValue('email'));

?>
<div class="section-wrapper margin-large-top margin-large-bottom">
    <div class="grid-container">
        <div class="grid-x grid-margin-x">
            <?php if (count($Settings['membera_area_contents']) > 1): ?>
                <div class="cell large-3 customer-area-sidebar">
                    <ul class="menu vertical">
                        <?php foreach ($Settings['membera_area_contents'] as $area):
                            if (!$User->hasPermission("fragment.customer-area--sidebar-article--{$area}")) {
                                continue;
                            }

                            ?>
                            <li <?= $cur_area == $area ? 'class="active"' : '' ?>>
                                <a href="<?= rex_getUrl(null, null, ['ctrl' => $area]) ?>"><?= \Wildcard::get('simpleshop.account_area_' . $area) ?></a>
                            </li>
                        <?php endforeach; ?>
                        <li>
                            <a href="<?= rex_getUrl(null, null, ['action' => 'logout', 'ts' => time()]) ?>">###action.logout###</a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="cell container <?= count($Settings['membera_area_contents']) > 1 ? 'large-9' : '' ?>">
                <?= $this->subfragment('simpleshop/customer/customer_area/' . $template); ?>
            </div>
        </div>
    </div>
</div>
