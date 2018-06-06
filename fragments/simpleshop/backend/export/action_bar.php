<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 19.10.17
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$status_options = $this->getVar('status_options');
$months         = $this->getVar('months');
$use_invoicing  = \FriendsOfREDAXO\Simpleshop\Utils::getSetting('use_invoicing', false);

?>
<div class="row">
    <div class="col-sm-1"><label><?= $this->i18n("label.filter"); ?>:</label></div>
    <div class="col-sm-2">
        <div class="rex-select-style">
            <select name="year-month" class="form-control">
                <?= implode('', $months) ?>
            </select>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="rex-select-style">
            <select name="status" class="form-control">
                <option value="">- <?= $this->i18n("label.all") ?> -</option>
                <?= implode('', $status_options) ?>
            </select>
        </div>
    </div>
    <div class="col-sm-7">
        <button class="btn btn-default float-left" type="submit" name="func" value="filter"><?= $this->i18n("update") ?></button>
        <?php if ($use_invoicing): ?>
            &nbsp;&nbsp;&nbsp;
            <button class="btn btn-apply float-left" type="submit" name="func" value="export-pdf">PDF <?= $this->i18n("action.export") ?></button>
        <?php endif; ?>
        &nbsp;&nbsp;&nbsp;
        <button class="btn btn-apply float-left" type="submit" name="func" value="export-csv">CSV <?= $this->i18n("action.export") ?></button>
        &nbsp;&nbsp;&nbsp;

        <?php
        $this->subfragment('simpleshop/backend/export/action_bar_extra_btn.php');
        ?>
    </div>
</div>
