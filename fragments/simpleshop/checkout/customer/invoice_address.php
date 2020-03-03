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

?>
<div class="addresses-grid margin-top margin-large-bottom">
    <h2 class="heading large">###label.invoice_address###</h2>
    <form action="<?= rex_getUrl(null, null, array_merge($_GET, ['ts' => time()])) ?>" method="POST">
        <?php
        $this->setVar('only_fields', true);
        $this->setVar('show_save_btn', false);
        $this->subfragment('simpleshop/customer/customer_area/address_form.php');
        ?>

        <div class="clearfix">
            <div class="margin-top">
                <button type="submit" class="button float-right">###action.go_ahead###</button>
            </div>
        </div>
    </form>
</div>