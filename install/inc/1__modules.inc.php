<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 09/04/2020
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


$sql     = rex_sql::factory();
$modules = glob($this->getPath('install/module/*/'));

foreach ($modules as $module) {
    $name   = trim(substr($module, strrpos($module, '/', -2)), '/');
    $input  = glob($module . 'input.php');
    $output = glob($module . 'output.php');

    if ($input || $output) {
        // check if already exists
        $sql->setTable('rex_module');
        $sql->setWhere(['name' => $name]);
        $sql->select();
        $_mod = $sql->getArray();

        if (empty ($_mod)) {
            if ($input) {
                $sql->setValue('input', file_get_contents($input[0]));
            }
            if ($output) {
                $sql->setValue('output', file_get_contents($output[0]));
            }
            $sql->setTable('rex_module');
            $sql->setValue('name', $name);
            $sql->setDebug(false);
            $sql->insert();
        }
    }
}