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

$sql             = rex_sql::factory();
$migrations_done = $this->getConfig('migrations', []);
$migrations      = glob($this->getPath('install') . '/db_migrations/*.php');
$isInstalled     = $this->getConfig('installed');


if (!$isInstalled) {
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
    $sql_files = glob($this->getPath('install') . '/sql/*.sql');

    foreach ($sql_files as $sql_file) {
        $sql->setQuery(file_get_contents($sql_file));
    }

    // install media manager types
    include_once __DIR__ . '/install/inc/mediatypes.inc.php';

    $this->setConfig('installed', true);
}


if ($isInstalled) {
    // for backward compatibility skip the initial migration file
    $migrations_done[] = $this->getPath('install') . '/db_migrations/2019-06-06 09-00-00__init.php';
}

foreach ($migrations as $migration) {
    if (in_array($migration, $migrations_done)) {
        continue;
    }

    try {
        $sql->beginTransaction();

        include_once $migration;

        $sql->commit();

        $migrations_done[] = $migration;
    } catch (ErrorException  $ex) {
        $sql->rollBack();
    }
}
$this->setConfig('migrations', $migrations_done);
