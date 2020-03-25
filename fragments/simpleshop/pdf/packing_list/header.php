<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 30.05.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$Order         = $this->getVar('Order');
$shipping_addr = $Order->getShippingAddress();
$barCodeImg    = $Order->getBarCodeImg();

if (file_exists(\rex_path::base('resources/img/logo300dpi.png'))) {
    $logo = \rex_path::base('resources/img/logo300dpi.png');
} else {
    $logo = \rex_path::base('resources/img/email/logo.png');
}

?>
<table width="100%" id="packing-list-header-infos">
    <tr>
        <td valign="top" class="logo">
            <img src="<?= $logo ?>" height="50"/>
        </td>
        <td valign="middle" class="company-data">
            <?php
            $this->setVar('contact_prefix', '<table style="font-size: 6pt;"><tr><td>', false);
            $this->setVar('contact_separator', '</td><td>', false);
            $this->setVar('contact_suffix', '</td></tr></table>', false);
            $this->subfragment('simpleshop/general/company-data.php')
            ?>
        </td>
        <td align="right" valign="top" class="bar-code">
            <?php if ($barCodeImg): ?>
                <img src="<?= $barCodeImg ?>" height="60">
            <?php endif; ?>
        </td>
    </tr>
</table>
<table width="100%" id="packing-list-info">
    <tr>
        <td valign="top" width="40%">
            <div id="ship-label">###label.shipping_document###<br/><br/></div>
            ###label.order_num###: <?= $Order->getReferenceId() ?><br/>
            ###label.shipping_number###: <?= $Order->getShippingKey() ?><br/>
            ###label.order_date###: <?= strftime('%d-%m-%Y', strtotime($Order->getValue('createdate'))) ?>
        </td>
        <td valign="top" class="address">
            <div id="address-label">###label.address###<br/><br/></div>
            <?php
            $this->setVar('address', $shipping_addr);
            $this->setVar('customer', null);
            $this->setVar('title', '');
            $this->subfragment('simpleshop/checkout/summary/address_item.php');
            ?>
        </td>
        <td width="20%" valign="top" align="center">
            <div id="page-label">###label.pages###<br/><br/></div>
            {PAGENO}/{nb}
        </td>
    </tr>
</table>
<table width="100%" id="packing-items">
    <thead>
    <tr>
        <td width="8%">#</td>
        <td width="14%">###label.code###</td>
        <td width="38%">###label.description###</td>
        <td width="10%">###label.amount###</td>
        <td width="15%">###label.single_price_no_vat###</td>
        <td width="15%">###label.total_sum###</td>
    </tr>
    </thead>
</table>
