<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 05.06.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$contact_prefix    = $this->getVar('contact_prefix', '');
$contact_separator = $this->getVar('contact_separator', '<br/>');
$contact_suffix    = $this->getVar('contact_suffix', '');


?>
<?= $contact_prefix ?>
    <div class="contact-company-data">
        <strong>###company.name###</strong><br/>
        ###company.street###<br/>
        ###company.postal### ###company.location### (###company.province###)<br/>
        ###company.region### - ###company.country###<br/>
        ###label.vat_short###: ###company.vat###<br/>
    </div>
<?= $contact_separator ?>
    <div class="contact-contact-data">
        ###company.phone###<br/>
        ###company.fax###<br/>
        <br/>
        ###company.email###<br/>
        ###company.website###
    </div>
<?= $contact_suffix ?>