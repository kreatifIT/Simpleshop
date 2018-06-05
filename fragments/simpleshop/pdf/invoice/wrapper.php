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

?>
<html>
<head>
    <title><?= $title ?></title>
    <meta name="author" content="###company.name###">
    <link rel="stylesheet" href="<?= rex_path::addonAssets('kreatif-mpdf', 'css/styles.css') ?>"/>
</head>
<body>
<?= $content ?>
</body>
</html>
