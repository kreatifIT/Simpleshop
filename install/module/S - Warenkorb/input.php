<?php

$id    = 1;
$id_settings    = 20;

$mform = new MForm();

$mform->addFieldset('Inhalt');
$mform->addTextAreaField("{$id}.title", ['label' => 'Titel (optional)']);
$mform->addTextAreaField("{$id}.text", ['label' => 'Beschreibung (optional)', 'class' => 'tinyMCEEditor']);

echo $mform->show();


/**
 * Settings
 */
$mform = new MForm();

$mform->addFieldset('Einstellungen');

$mform->addSelectField("{$id_settings}.margin", [
    'margin-top margin-bottom' => 'Abstand oben + unten',
    'margin-top'                     => 'Abstand nur oben',
    'margin-bottom'                  => 'Abstand nur unten',
    'margin-large-top margin-large-bottom' => 'großer Abstand oben + unten',
    'margin-large-top'                     => 'großer Abstand nur oben',
    'margin-large-bottom'                  => 'großer Abstand nur unten',
    ''                                     => 'keinen',
], [
    'label' => 'Block-Abstand',
    'class' => '',
], null, null, 'margin-top margin-bottom');

echo $mform->show();
