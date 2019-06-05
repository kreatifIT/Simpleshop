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

rex_dir::create(rex_path::addonData('simpleshop', 'log'), true);
rex_dir::create(rex_path::addonData('simpleshop', 'packing_lists'), true);

if (!$this->hasConfig()) {

    $sql     = rex_sql::factory();
    $modules = glob(__DIR__ . '/install/module/*/');


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


    // import tables
    $tablesets = glob($this->getPath('install') . '/tablesets/*.json');
    $sql_files = glob($this->getPath('install') . '/sql/*.sql');

    foreach ($tablesets as $tableset) {
        \rex_yform_manager_table_api::importTablesets(file_get_contents($tableset));
    }

    foreach ($sql_files as $sql_file) {
        $sql->setQuery(file_get_contents($sql_file));
    }

    // install media manager types
    include_once __DIR__ . '/install/inc/mediatypes.inc.php';
}

\rex_sql_table::get(\FriendsOfREDAXO\Simpleshop\Variant::TABLE)
    ->ensureColumn(new \rex_sql_column('prio', 'int(11)', false, 0))
    ->alter();
    $this->setConfig('installed', true);
