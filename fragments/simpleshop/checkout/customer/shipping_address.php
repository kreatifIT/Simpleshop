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

$useShAddress = $this->getVar('use_shipping_address', false);
$Address      = $this->getVar('Address', null);
$back_url     = $this->getVar('back_url');

?>
<div class="addresses-grid margin-top margin-large-bottom">

    <h2 class="heading large">###label.shipping_address###</h2>

    <form method="POST" action="" class="shipping-form">
        <div class="address-toggle margin-top margin-large-bottom">
            <div class="custom-checkbox">
                <label class="custom-checkbox">
                    <input type="checkbox" name="shipping_address_is_idem" value="1" <?= $useShAddress ? '' : 'checked="checked"' ?>
                           onchange="Simpleshop.toggleShipping(this, '.shipping-form|.address-wrapper')"/>
                    ###simpleshop.shipping_address_is_idem###
                    <span class="checkbox"></span>
                </label>
            </div>
        </div>
        <div class="address-wrapper <?= $useShAddress ? '' : 'hide' ?>">
            <?php
            $this->setVar('only_fields', true);
            $this->setVar('show_save_btn', false);
            $this->subfragment('simpleshop/customer/customer_area/address_form.php');
            ?>
        </div>

        <div class="margin-top">
            <a href="<?= $back_url ?>" class="button hollow">###action.go_back###</a>
            <button type="submit" class="button float-right">###action.go_ahead###</button>
        </div>
    </form>
</div>