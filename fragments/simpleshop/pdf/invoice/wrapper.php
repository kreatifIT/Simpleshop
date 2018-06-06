<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 30.05.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$content = $this->getVar('content');
$title   = $this->getVar('title');
$debug   = $this->getVar('debug', false);
$css_url = $debug ? rex_url::addonAssets('kreatif-mpdf', 'css/styles.css') : rex_path::addonAssets('kreatif-mpdf', 'css/styles.css');

?>
<html>
<head>
    <title><?= $title ?></title>
    <meta name="author" content="###company.name###">
    <link rel="stylesheet" href="<?= $css_url ?>"/>
</head>
<body>
<div id="invoice-container">
    <?= $content ?>
</div>
</body>
</html>
