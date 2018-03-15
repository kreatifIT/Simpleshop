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
    <div class="row column">
        <h2>###label.invoice_address###</h2>

        <form action="" method="POST">
            <?php
            $this->setVar('only_fields', true);

            $this->setVar('show_save_btn', false);
            $this->setVar('real_field_names', true);
            $this->setVar('excluded_fields', ['email', 'password']);
            $this->subfragment('simpleshop/customer/customer_area/account_form.php');

            $this->setVar('show_save_btn', true);
            $this->setVar('excluded_fields', ['firstname', 'lastname', 'company_name']);
            $this->subfragment('simpleshop/customer/customer_area/address_form.php');
            ?>
        </form>
    </div>
</div>