<?php

$notice = [];
if ($this->getElement('notice') != '') {
    $notice[] = rex_i18n::translate($this->getElement('notice'), false);
}
if (isset($this->params['warning_messages'][$this->getId()]) && !$this->params['hide_field_warning_messages']) {
    $notice[] = '<span class="text-warning">' . rex_i18n::translate($this->params['warning_messages'][$this->getId()], false) . '</span>'; //    var_dump();
}
if (count($notice) > 0) {
    $notice = '<p class="help-block">' . implode('<br />', $notice) . '</p>';
}
else {
    $notice = '';
}

$class       = $this->getElement('required') ? 'form-is-required ' : '';
$class_group = trim('form-group ' . $class . $this->getWarningClass());

$class_label[] = 'control-label';

$attributes       = [];
$attributes['id'] = $this->getFieldId();
if ($multiple) {
    $attributes['name']     = $this->getFieldName() . '[]';
    $attributes['multiple'] = 'multiple';
}
else {
    $attributes['name'] = $this->getFieldName();
}
if ($size > 1) {
    $attributes['size'] = $size;
}

$attributes['class'] = 'form-control custom-select2 product-select2' . $attributes['class'];

$attributes = $this->getAttributeElements($attributes, ['autocomplete', 'pattern', 'required', 'disabled', 'readonly']);
$options    = array_filter($lvalues);

?>
<div class="<?= $class_group ?>" id="<?= $this->getHTMLId() ?>">
    <label class="<?= implode(' ', $class_label) ?>" for="<?= $this->getFieldId() ?>">
        <?= $this->getLabel() ?>
    </label>
    <div class="yform-select2-container">
        <select <?= implode(' ', $attributes) ?>>
            <?php foreach ($options as $key => $value): ?>
                <option value="<?= $key ?>" <?= in_array((string) $key, $this->getValue(), true) ? 'selected="selected"' : '' ?>>
                    <?= $this->getLabelStyle($value) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?= $notice ?>
</div>
