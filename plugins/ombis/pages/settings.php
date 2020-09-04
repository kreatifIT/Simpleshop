<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 15/06/2020
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


$fragment = new \rex_fragment();
$fragment->setVar('Addon', $this);
$fragment->setVar('key', 'simpleshop.Settings.Ombis');
$fragment->setVar('fragment_path', 'simpleshop/backend/ombis_settings.php');
echo $fragment->parse('simpleshop/backend/settings_wrapper.php');
