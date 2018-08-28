<?php

$Settings    = rex::getConfig('simpleshop.Settings');
$Addon       = rex_addon::get('simpleshop');
$setting_url = rex_url::backendPage('simpleshop/settings');
$options     = [];

foreach ((array)$Settings['membera_area_contents'] as $value) {
    $options[$value] = $Addon->i18n('settings.content_' . $value);
}

$id    = 1;
$mform = new MForm();

$mform->addFieldset('Inhalt');

if (count($options) == 0) {
    $mform->addHtml('
        <small style="margin-bottom:20px;display:block;color:red;">
            Es sind keine Typen/Kundenbereich-Inhalte verfügbar bitte <a href="' . $setting_url . '">hier</a> einstellen
        </small>
    ');
}
$mform->addTextAreaField("{$id}.title", ['label' => 'Titel (optional)']);
$mform->addTextAreaField("{$id}.text", ['label' => 'Beschreibung (optional)', 'class' => 'tinyMCEEditor']);
$mform->addSelectField("{$id}.content", $options, ['label' => 'Typ']);

echo $mform->show();