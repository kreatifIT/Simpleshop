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


$tablesets = glob(__DIR__ . '/../tableset/*.json');


foreach ($tablesets as $filepath) {
    $filename  = basename($filepath);
    $tablename = str_replace('.json', '', $filename);
    $table     = rex_sql_table::get($tablename);

    if (!$table->exists()) {
        $tableset = file_get_contents($filepath);
        rex_yform_manager_table_api::importTablesets($tableset);
    }
}
