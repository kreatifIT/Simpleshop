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

$status  = '';
$hiddens = [];
$columns = [];
$vkey    = $this->getVar('vkey');
$name    = $this->getVar('name');
$fields  = $this->getVar('fields');
$Variant = $this->getVar('Variant');


foreach ($fields as $field) {
    $field->params['this']->setObjectparams('form_name', $vkey);
    $field->setId($field->name);
    $field->init();
    $field->setLabel('');
    $field->setValue($Variant ? $Variant->getValue($field->name) : null);
    $field->enterObject();

    if ($field->name == 'type') {
        $status = $field->getValue();
    }

    if ($field->type == 'hidden_input' || $field->name == 'prio') {
        $hiddens[] = $field->params['form_output'][$field->getId()];
    }
    else {
        $columns[] = "<td>{$field->params['form_output'][$field->getId()]}</td>";
    }
}

?>
<tr class="type-<?= $status ?> item">
    <td class="rex-table-icon sort-handle ui-sortable-handle">
        <i class="rex-icon fa fa-bars sort-icon"></i>
    </td>
    <td>
    <span class="variant-name">
        <?= implode('<br/></span><span style="white-space:nowrap;">+ ', $name) ?>
    </span>
    </td>
    <?= implode('', $columns) ?>
    <td><?= implode('', $hiddens) ?></td>
</tr>



