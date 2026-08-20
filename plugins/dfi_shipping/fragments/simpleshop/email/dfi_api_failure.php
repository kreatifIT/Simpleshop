<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$order        = $this->getVar('order');
$errorMessage = $this->getVar('errorMessage');
$fallbackCost = $this->getVar('fallbackCost');

?>
Die Versandkosten-Berechnung über die DFI-Schnittstelle ist fehlgeschlagen. Es wurden stattdessen die allgemein hinterlegten Versandkosten verwendet.<br/>
<br/>
<strong>Bestellung:</strong> <?= $order->getReferenceId() ?><br/>
<strong>Fehler:</strong> <?= $errorMessage ?><br/>
<strong>Verwendete Versandkosten:</strong> &euro; <?= format_price($fallbackCost) ?><br/>
