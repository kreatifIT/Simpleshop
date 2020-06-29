<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 22.06.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


$includeFiles = glob($this->getPath('install') . '/ensure_db_structure/*.php');

$sql = rex_sql::factory();
$sql->setQuery('SET FOREIGN_KEY_CHECKS = 0');

try {
    foreach ($includeFiles as $file) {
        include_once $file;
    }
}
finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}