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

$migrations_done = $this->getConfig('migrations', []);
$isInstalled     = $this->getConfig('installed');
$migrations      = glob($this->getPath('install/db_migrations/*.php'));


if ($isInstalled) {
    $migrations_done[] = $this->getPath('install') . '/db_migrations/2019-06-06 09-00-00.php';
} else {
    // bypass all migrations before v4.0.0
    foreach ($migrations as $filepath) {
        $migrations_done[] = $filepath;
        $filename = basename($filepath);

        if ($filename == '2020-04-09 10-15-00.php') {
            break;
        }
    }
}

$migrationsTodo = array_diff($migrations, $migrations_done);

$sql = rex_sql::factory();
foreach ($migrationsTodo as $migration) {
    try {
        $sql->beginTransaction();
        include_once $migration;
        $sql->commit();
        $migrations_done[] = $migration;
    } catch (ErrorException  $ex) {
        $sql->rollBack();
    }
}
$this->setConfig('migrations', array_unique($migrations_done));