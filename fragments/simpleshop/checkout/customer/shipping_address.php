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

$this->setVar('btn_label', '###action.go_ahead###');

$useShAddress      = $this->getVar('use_shipping_address', \rex_request('shipping_address_is_idem', 'int') == 1);
$createNewShipping = $this->getVar('create_new_address', \rex_request('create_new_shipping_address', 'int') == 1);
$addresses         = $this->getVar('addresses', []);
$orderAddress      = $this->getVar('Address');
$back_url          = $this->getVar('back_url');

?>
<div class="addresses-grid margin-top margin-large-bottom">

    <h2 class="heading large">###label.shipping_address###</h2>

    <form method="POST" action="<?= rex_getUrl(null, null, array_merge($_GET, ['ts' => time()])) ?>" class="shipping-form">
        <div class="address-toggle margin-top margin-bottom">
            <div class="custom-checkbox">
                <label class="custom-checkbox">
                    <input type="checkbox" name="shipping_address_is_idem" value="1" <?= $useShAddress ? '' : 'checked="checked"' ?>
                           onchange="Simpleshop.toggleShipping(this, '.shipping-form|.address-wrapper', true)"/>
                    ###label.shipping_address_is_idem###
                    <span class="checkbox"></span>
                </label>
            </div>
        </div>
        <div class="address-wrapper <?= $useShAddress ? '' : 'hide' ?>">

            <?php if ($useShAddress && !empty($_POST)): ?>
                <div class="callout alert">
                    ###error.choose_shipping_address###
                </div>
            <?php endif; ?>

            <?php if (count($addresses)): ?>
                <h5>###label.use_existing_addredss###</h5>
                <div class="grid-x grid-margin-x shipping-addresses-wrapper margin-bottom" data-shipping-addresses>
                    <?php foreach ($addresses as $address): ?>
                        <div class="cell medium-6 large-4">
                            <div class="address-item-wrapper">
                                <div class="custom-radio">
                                    <label>
                                        <input type="radio" name="shipping-address" value="<?= $address->getId() ?>" <?= $orderAddress->getId() == $address->getId() ? 'checked="checked"' : '' ?>>
                                        <span class="radio"></span>

                                        <?php
                                        $this->setVar('address', $address);
                                        $this->subfragment('simpleshop/customer/customer_area/address_data.php');
                                        ?>

                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (count($addresses)): ?>
                <div class="custom-checkbox">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="create_new_shipping_address" value="1" <?= $createNewShipping ? 'checked="checked"' : '' ?>
                               onchange="Simpleshop.toggleShipping(this, '.address-wrapper|.new-address-wrapper')"/>
                        ###action.create_new_shipping_address###
                        <span class="checkbox"></span>
                    </label>
                </div>
            <?php else: ?>
                <h5>###action.create_new_shipping_address###</h5>
            <?php endif; ?>
            <div class="new-address-wrapper <?= count($addresses) && $createNewShipping != 1 ? 'hide' : '' ?>">
                <?php
                $fragment = new \rex_fragment();
                $fragment->setVar('only_fields', true);
                $fragment->setVar('show_save_btn', false);
                $fragment->setVar('form_id', 'shipping-address');
                echo $fragment->parse('simpleshop/customer/customer_area/address_form.php');
                ?>
            </div>
        </div>

        <div class="clearfix">
            <div class="margin-top">
                <a href="<?= $back_url ?>" class="button hollow">###action.go_back###</a>
                <button type="submit" class="button float-right">###action.go_ahead###</button>
            </div>
        </div>
    </form>
</div>