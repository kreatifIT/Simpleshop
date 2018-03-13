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


$fields           = $this->getVar('additional_fields', []);
$back_url         = $this->getVar('back_url', '');
$show_save_btn    = $this->getVar('show_save_btn', true);
$callback_on_save = $this->getVar('callback_on_save', null);
$btn_label        = $this->getVar('btn_label', ucfirst(\Wildcard::get('action.save')));
$Customer         = Customer::getCurrentUser();
$addresses        = CustomerAddress::getAll(true, [
    'filter'  => [['customer_id', $Customer->getId()]],
    'limit'   => 1,
    'orderBy' => 'id',
    'order'   => 'desc',
])->toArray();
$address          = array_shift($addresses);


if ($Customer->getValue('ctype') == 'company') {
    $fields = array_merge($fields, ['firstname', 'lastname']);
}
else {
    $fields = array_merge($fields, ['company_name']);
}

$id   = 'address-data-' . \rex_article::getCurrentId();
$sid  = "form-{$id}";
$form = Customer::getAddressFieldForm(\Kreatif\Form::factory(), $Customer->getId(), $address, $fields);
$form->setObjectparams('form_anchor', '-' . $sid);
$form->setObjectparams('form_name', $sid);
$form->setValueField('html', ['', '<input type="hidden" name="yform-id" value="' . $sid . '">']);

if ($show_save_btn) {
    // Submit
    $form->setValueField('html', ['', '<div class="column margin-small-top">']);

    if ($back_url) {
        $form->setValueField('html', ['', '<a href="'. $back_url .'" class="button">###action.go_back###</a>']);
    }

    $form->setValueField('submit', [
        'name'        => 'submit-' . $sid,
        'no_db'       => 'no_db',
        'labels'      => $btn_label,
        'css_classes' => 'button secondary float-right',
    ]);
    $form->setValueField('html', ['', '</div>']);
}

$formOutput = $form->getForm();

if ($form->isSend() && !$form->hasWarnings()) {
    if ($callback_on_save) {
        $formOutput = call_user_func_array($callback_on_save, [$formOutput, $form]);
    }
    $formOutput = '<div class="column"><div class="callout success">###simpleshop.data_saved###</div></div> ' . $formOutput;
}

?>
<div class="account-data margin-small-top margin-bottom">
    <div class="row">

        <?= $formOutput ?>

    </div>
</div>
