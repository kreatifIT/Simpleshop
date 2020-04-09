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
$queries = glob(__DIR__ . '/../sql/*.sql');


foreach ($queries as $filepath) {
    $filename  = basename($filepath);
    $tablename = str_replace('.sql', '', $filename);
    $item      = current($sql->getArray("SELECT id FROM {$tablename} LIMIT 1"));

    if (!$item) {
        $query = file_get_contents($filepath);
        $sql->setQuery($query);
    }
}