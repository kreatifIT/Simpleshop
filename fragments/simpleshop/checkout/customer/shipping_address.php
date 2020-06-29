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

$addresses    = $this->getVar('addresses', []);
$orderAddress = $this->getVar('Address');
$back_url     = $this->getVar('back_url');
$errors       = $this->getVar('errors');

$addNewAddress = isset(rex_post('FORM', 'array')['shop-shipping-address']);

?>
<div class="addresses-grid margin-top margin-large-bottom" data-address-toggle-container>

    <h2 class="heading large">###label.shipping_address###</h2>

    <form method="POST" action="<?= rex_getUrl(null, null, array_merge($_GET, ['ts' => time()])) ?>" class="shipping-form margin-top" id="dflt-shipping-address-wrapper">
        <div class="address-wrapper">

            <?php if (count($errors)): ?>
                <div class="callout alert">
                    - <?= implode('<br/>- ', $errors) ?>
                </div>
            <?php endif; ?>

            <div class="grid-x grid-margin-x shipping-addresses-wrapper margin-bottom" data-shipping-addresses>
                <?php foreach ($addresses as $address): ?>
                    <?php
                    $addressData = $address->toAddressArray();
                    ?>
                    <div class="cell medium-6 large-4 margin-small-bottom">
                        <div class="address-item-wrapper">
                            <div class="custom-radio">
                                <label>
                                    <input type="radio" name="shipping-address" value="<?= $address->getId() ?>" <?= !$addNewAddress && $orderAddress->getId() == $address->getId() ? 'checked="checked"' : '' ?>>
                                    <span class="radio"></span>
                                    <?= implode('<br/>', $addressData); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="custom-checkbox">
                <label class="custom-checkbox">
                    <input type="checkbox" name="create_new_shipping_address" value="1" <?= $addNewAddress ? 'checked="checked"' : '' ?>
                           onchange="Simpleshop.toggleShipping(this)"/>
                    ###action.create_new_shipping_address###
                    <span class="checkbox"></span>
                </label>
            </div>
        </div>

        <div class="checkout-buttons <?= $addNewAddress ? 'hide' : '' ?>" data-address-toggle="existing-address">
            <a href="<?= $back_url ?>" class="button hollow">###action.go_back###</a>
            <button type="submit" name="action" value="apply-shipping-address" class="button">###action.go_ahead###</button>
        </div>
    </form>
    <div class="new-address-wrapper margin-small-top <?= $addNewAddress ? '' : 'hide' ?>" data-address-toggle="new-address">

        <?php
        $this->setVar('Address', CustomerAddress::create());
        $this->setVar('save_callback', function ($address) {
            $Order = Session::getCurrentOrder();
            $Order->setValue('shipping_address', $address);
            $Order->setValue('shipping_address_id', $address->getId());
            Session::setCheckoutData('Order', $Order);

            $redirectUrl = html_entity_decode(rex_getUrl(null, null, array_merge($_GET, ['ts' => time()])));
            \rex_response::sendCacheControl();
            \rex_response::sendRedirect($redirectUrl);
        });
        $this->subfragment('simpleshop/customer/customer_area/shipping_address.php');
        ?>
    </div>

</div>
