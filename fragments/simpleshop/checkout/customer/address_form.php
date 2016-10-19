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

$addresses  = [];
$User       = Customer::getCurrentUser();
$user_id    = $User->getValue('id');
$_addresses = CustomerAddress::query()
    ->where('customer_id', $user_id)
    ->find();

$addresses[0] = isset($_addresses[0]) ? $_addresses[0] : CustomerAddress::create();
$addresses[1] = isset($_addresses[1]) ? $_addresses[1] : CustomerAddress::create();

$yform = new \rex_yform();
$yform->setObjectparams('error_class', 'row column form_warning');
$yform->setObjectparams('submit_btn_show', FALSE);
$yform->setObjectparams('form_name', 'customer_address');
$yform->setObjectparams('form_action', '');
$yform->setObjectparams('form_class', 'row column');
$yform->setObjectparams('form_ytemplate', 'form,bootstrap');
$yform->setObjectparams('form_showformafterupdate', TRUE);

$yform->setValueField('radio', ['customer_address.0.salutation', '###label.gender###', '###label.female###=Miss,###label.male###=Mr', $addresses[0]->getValue('salutation') ?: 'Miss']);
$yform->setValueField('text', ['customer_address.0.firstname', '###label.firstname###', $addresses[0]->getValue('firstname')]);
$yform->setValueField('text', ['customer_address.0.lastname', '###label.lastname###', $addresses[0]->getValue('lastname')]);
$yform->setValueField('text', ['customer_address.0.additional', '###label.addition###', $addresses[0]->getValue('additional')]);
$yform->setValueField('text', ['customer_address.0.street', '###label.street###', $addresses[0]->getValue('street')]);
$yform->setValueField('text', ['customer_address.0.location', '###label.location### / ###label.postal###', $addresses[0]->getValue('location')]);
$yform->setValueField('text', ['customer_address.0.zip', '', $addresses[0]->getValue('zip')]);
$yform->setValueField('text', ['customer_address.0.phone', '###label.phone###', $addresses[0]->getValue('phone')]);
$yform->setValueField('radio', ['customer_address.0.type', '###shop.client_typ###', '###label.private_customer###=P,###label.company###=C', '', $addresses[0]->getValue('type') ?: 'P']);
$yform->setValueField('text', ['customer_address.0.vat_num', '###label.vat_short###', $addresses[0]->getValue('vat_num')]);
$yform->setValueField('text', ['customer_address.0.fiscal_code', '###label.fiscal_code###', $addresses[0]->getValue('fiscal_code')]);
$yform->setValueField('hidden', ['customer_address.0.id', $addresses[0]->getValue('id')]);

$yform->setValueField('html', ['opening_tag', '<div class="form-group"><label>###shop.shipping_address###</label>']);
$yform->setValueField('checkbox', ['use_shipping_address', '###shop.use_alternative_shipping_address###']);
$yform->setValueField('html', ['closing_tag', '</div>']);

$yform->setValueField('text', ['customer_address.1.firstname', '###label.firstname###', $addresses[1]->getValue('firstname')]);
$yform->setValueField('text', ['customer_address.1.lastname', '###label.lastname###', $addresses[1]->getValue('lastname')]);
$yform->setValueField('text', ['customer_address.1.additional', '###label.addition###', $addresses[1]->getValue('additional')]);
$yform->setValueField('text', ['customer_address.1.street', '###label.street###', $addresses[1]->getValue('street')]);
$yform->setValueField('text', ['customer_address.1.location', '###label.location### / ###label.postal###', $addresses[1]->getValue('location')]);
$yform->setValueField('text', ['customer_address.1.zip', '', $addresses[1]->getValue('zip')]);
$yform->setValueField('hidden', ['customer_address.1.id', $addresses[1]->getValue('id')]);

$yform->setValueField('html', ['opening_tag', '
    <div class="row small-up-2">
        <div class="column"><a href="#" class="button warning">###action.go_back###</a></div>
        <div class="column">
']);
$yform->setValueField('submit', ['', '###action.go_ahead###', null, null, null, 'button float-right']);
$yform->setValueField('html', ['closing_tag', '</div></div>']);

$yform->setFieldValue('send', !empty ($_POST), '', 'send');
$yform->setHiddenField('customer_id', $user_id);

// TODO: add validations
$yform->setValidateField('empty', array("customer_address.0.firstname","Bitte tragen Sie den Vornamen ein"));

$yform->setActionField('callback', ['\FriendsOfREDAXO\Simpleshop\CustomerAddress::action__save_checkout_address']);

echo $yform->getForm();