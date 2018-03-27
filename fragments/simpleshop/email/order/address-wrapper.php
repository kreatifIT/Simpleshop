<?php

$config   = $this->getVar('config', []);
$shipping = $this->getVar('shipping_address', null);
$invoice  = $this->getVar('invoice_address', null);
$styles   = \FriendsOfREDAXO\Simpleshop\FragmentConfig::getValue('styles');

?>
<!-- address -->
<table class="callout" <?= $styles['callout'] ?>>
    <tr <?= $styles['tr'] ?>>
        <th class="callout-inner" <?= $styles['callout_inner'] ?>>
            <table class="row" <?= $styles['body'] ?>>
                <tbody>
                <tr <?= $styles['tr'] ?>>
                    <th class="small-12 <?= $shipping ? 'large-6' : '' ?> columns first" <?= $styles['th'] ?>>
                        <table <?= $styles['body'] ?>>
                            <tr <?= $styles['tr'] ?>>
                                <th <?= $styles['th'] ?>>
                                    <?php
                                    $this->setVar('address', $invoice);
                                    $this->setVar('title', '###label.invoice_address###');
                                    $this->subfragment('simpleshop/checkout/summary/address_item.php');
                                    ?>
                                </th>
                            </tr>
                        </table>
                    </th>

                    <?php if ($shipping): ?>
                        <th class="small-12 large-6 columns last" <?= $styles['th'] ?>>
                            <table <?= $styles['body'] ?>>
                                <tr <?= $styles['tr'] ?>>
                                    <th <?= $styles['th'] ?>>
                                        <?php
                                        $this->setVar('address', $shipping);
                                        $this->setVar('type', 'shipping');
                                        $this->setVar('title', '###label.shipping_address###');
                                        $this->subfragment('simpleshop/checkout/summary/address_item.php');
                                        ?>
                                    </th>
                                </tr>
                            </table>
                        </th>
                    <?php endif; ?>

                </tr>
                </tbody>
            </table>
        </th>
        <th class="expander" <?= $styles['th'] ?>></th>
    </tr>
</table>