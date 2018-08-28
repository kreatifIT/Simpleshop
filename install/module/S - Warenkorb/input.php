<?php

$id    = 1;
$mform = new MForm();

$mform->addFieldset('Inhalt');
$mform->addTextAreaField("{$id}.title", ['label' => 'Titel (optional)']);
$mform->addTextAreaField("{$id}.text", ['label' => 'Beschreibung (optional)', 'class' => 'tinyMCEEditor']);

echo $mform->show();