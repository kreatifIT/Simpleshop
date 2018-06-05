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

$Customer = $this->getVar('Customer');


if (file_exists(\rex_path::base('resources/img/logo300dpi.png'))) {
    $logo = \rex_path::base('resources/img/email/logo300dpi.png');
}
else {
    $logo = \rex_path::base('resources/img/email/logo.png');
}

?>
    <div style="text-align:right;">
        <img src="<?= $logo ?>" height="50"/>
    </div>
    <table width="100%" id="invoice-header-infos">
        <tr>
            <td width="50%" valign="top">
                <?php
                $this->setVar('address', $Customer);
                $this->setVar('title', '###shop.invoice_address###');
                $this->subfragment('simpleshop/checkout/summary/address_item.php');
                ?>
            </td>
            <td width="50%" align="right" valign="top">
                <div>
                    <strong>###company.name###</strong><br/>
                    ###company.street###<br/>
                    ###company.postal### ###company.location### (###company.province###)<br/>
                    ###label.vat_short###: ###company.vat###<br/>
                    ###company.shop_invoice_info###
                </div>
            </td>
        </tr>
    </table>