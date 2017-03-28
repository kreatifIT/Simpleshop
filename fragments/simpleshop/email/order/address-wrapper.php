<?php

$config   = $this->getVar('config', []);
$shipping = $this->getVar('shipping_address', null);
$invoice  = $this->getVar('invoice_address', null);
$styles   = array_merge([
    'th'            => '',
    'tr'            => '',
    'body'          => '',
    'callout'       => '',
    'callout_inner' => '',
], $config['email_tpl_styles']);

if ($invoice):
    ?>
    <!-- address -->
    <table class="callout" style="<?= $styles['callout'] ?>">
        <tr style="<?= $styles['tr'] ?>">
            <th class="callout-inner" style="<?= $styles['callout_inner'] ?>">
                <table class="row" style="<?= $styles['body'] ?>">
                    <tbody>
                    <tr style="<?= $styles['tr'] ?>">
                        <th class="small-12 <?= $shipping ? 'large-6' : '' ?> columns first" style="<?= $styles['th'] ?>margin:0 auto;padding-bottom:16px;width:<?= $shipping ? '50' : '100' ?>%;">
                            <table style="<?= $styles['body'] ?>">
                                <tr style="<?= $styles['tr'] ?>">
                                    <th style="<?= $styles['th'] ?>">
                                        <?php
                                        $this->setVar('address', $invoice);
                                        $this->setVar('title', '###shop.invoice_address###');
                                        $this->subfragment('simpleshop/checkout/summary/address_item.php');
                                        ?>
                                    </th>
                                </tr>
                            </table>
                        </th>

                        <?php if ($shipping): ?>
                            <th class="small-12 large-6 columns last" style="<?= $styles['th'] ?>margin:0 auto;padding-bottom:16px;width:50%;">
                                <table style="<?= $styles['body'] ?>">
                                    <tr style="<?= $styles['tr'] ?>">
                                        <th style="<?= $styles['th'] ?>">
                                            <?php
                                            $this->setVar('address', $shipping);
                                            $this->setVar('type', 'shipping');
                                            $this->setVar('title', '###shop.shipping_address###');
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
            <th class="expander" style="<?= $styles['th'] ?>"></th>
        </tr>
    </table>
<?php endif;