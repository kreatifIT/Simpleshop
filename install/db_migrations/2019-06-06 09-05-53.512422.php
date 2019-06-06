<?php

$sql = rex_sql::factory();
$sql->setQuery('SET FOREIGN_KEY_CHECKS = 0');

try {
    rex_sql_table::get('rex_shop_order')
        ->ensureColumn(new rex_sql_column('ipn_transaction_id', 'text', false, null, null), 'ref_order_id')
        ->alter();

    $sql->setQuery(<<<'SQL'
        INSERT INTO `rex_yform_field` (`table_name`, `prio`, `type_id`, `type_name`, `db_type`, `list_hidden`, `search`, `name`, `label`, `not_required`, `scale`, `options`, `default`, `no_db`, `type`, `message`, `empty_value`, `notice`, `format`, `only_empty`, `fields`, `table`, `field`, `empty_option`, `widget`, `attributes`, `preview`, `multiple`, `values`, `relation_table`, `function`, `salt`, `filter`, `current_date`, `html`, `show_value`, `params`, `rules`, `precision`, `choices`, `expanded`, `unit`, `validate_type`)
        VALUES
            ('rex_shop_order', 5, 'value', 'hidden_input', 'text', 1, 0, 'ipn_transaction_id', 'IPN Transaction ID', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')
        ON DUPLICATE KEY UPDATE `table_name` = VALUES(`table_name`), `prio` = VALUES(`prio`), `type_id` = VALUES(`type_id`), `type_name` = VALUES(`type_name`), `db_type` = VALUES(`db_type`), `list_hidden` = VALUES(`list_hidden`), `search` = VALUES(`search`), `name` = VALUES(`name`), `label` = VALUES(`label`), `not_required` = VALUES(`not_required`), `scale` = VALUES(`scale`), `options` = VALUES(`options`), `default` = VALUES(`default`), `no_db` = VALUES(`no_db`), `type` = VALUES(`type`), `message` = VALUES(`message`), `empty_value` = VALUES(`empty_value`), `notice` = VALUES(`notice`), `format` = VALUES(`format`), `only_empty` = VALUES(`only_empty`), `fields` = VALUES(`fields`), `table` = VALUES(`table`), `field` = VALUES(`field`), `empty_option` = VALUES(`empty_option`), `widget` = VALUES(`widget`), `attributes` = VALUES(`attributes`), `preview` = VALUES(`preview`), `multiple` = VALUES(`multiple`), `values` = VALUES(`values`), `relation_table` = VALUES(`relation_table`), `function` = VALUES(`function`), `salt` = VALUES(`salt`), `filter` = VALUES(`filter`), `current_date` = VALUES(`current_date`), `html` = VALUES(`html`), `show_value` = VALUES(`show_value`), `params` = VALUES(`params`), `rules` = VALUES(`rules`), `precision` = VALUES(`precision`), `choices` = VALUES(`choices`), `expanded` = VALUES(`expanded`), `unit` = VALUES(`unit`), `validate_type` = VALUES(`validate_type`)
SQL
    );
} finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}
