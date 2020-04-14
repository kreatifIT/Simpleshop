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
    $field->init();
    $field->setLabel('');

    if ($field instanceof \rex_yform_value_be_table || ($field instanceof \rex_yform_value_be_manager_relation && $field->elements['type'] == 6)) {
        $field->setId('be_table|' . $vkey . '|' . $field->fieldIndex);
        $field->setName(str_replace(',', '-', $vkey . '-' . $field->fieldIndex));

        if ($field instanceof \rex_yform_value_be_manager_relation && $field->elements['type'] == 6) {
            $field->params['main_id'] = $Variant->getId();
            $field->setValue(null);
        } else {
            $field->setValue($Variant ? $Variant->getRawValue($field->fieldName) : null);
        }
    } else {
        $field->setId($field->name);
        $field->setValue($Variant ? $Variant->getValue($field->fieldName) : null);
    }
    $field->enterObject();

    if ($field->name == 'type') {
        $status = $field->getValue();
    }

    if ($field->type == 'hidden_input' || $field->name == 'prio') {
        $hiddens[] = $field->params['form_output'][$field->getId()];
    } else {
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
        <?= implode('<br/></span><span style="white-space:nowrap;">+ ', array_filter($name)) ?>
    </span>
    </td>
    <?= implode('', $columns) ?>
    <td><?= implode('', $hiddens) ?></td>
</tr>



