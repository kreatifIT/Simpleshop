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

namespace FriendsOfREDAXO\Simpleshop;

echo \rex_view::title('Simpleshop');

$_FUNC     = rex_post('func', 'string');
$order_ids = rex_post('orders', 'array', []);
$output    = rex_request('output', 'string', 'file');
$status    = rex_request('status', 'string');

list ($year, $month) = explode('-', rex_request('year-month', 'string', date('Y-m')));

$statuses     = [];
$_status_opts = [];
$_options     = explode(',', \rex_yform_manager_table::get(Order::TABLE)->getValueField('status')->getElement('options'));

foreach ($_options as $option) {
    list ($value, $key) = explode('=', $option);
    $statuses[trim($key)] = trim($value);
    $_status_opts[] = '<option value="'. $key .'" '. ($key == $status ? 'selected="selected"' : '') .'>'. trim($value) .'</option>';
}

if ($_FUNC == 'export' && count($order_ids)) {
    ob_clean();

    if ($output == 'file') {
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment;filename=orders-{$year}-{$month}.csv");
    }
    else {
        header('Content-Type: text/html; charset=utf-8');
    }
    $fragment = new \rex_fragment();
    $fragment->setVar('order_ids', $order_ids);
    $fragment->setVar('output', $output);
    $fragment->setVar('statuses', $statuses);
    echo $fragment->parse('simpleshop/backend/export/orders_export.php');
    exit;
}

$orders = Order::query()->where('createdate', "{$year}-{$month}-01", '>=')->where('createdate', date('Y-m-d 23:59:59', strtotime("{$year}-{$month} next month -1 day")), '<=')->orderBy('id');

if ($status != '') {
    $orders->where('status', $status);
}
$orders = $orders->find();


$sections   = '';
$ym_options = [];

$sql = \rex_sql::factory();
$sql->setQuery("SELECT CONCAT(YEAR(MIN(createdate)), '-', MONTH(MIN(createdate))) AS min_date FROM " . Order::TABLE);

$begin     = new \DateTime($sql->getValue('min_date') . '-01');
$end       = new \DateTime();
$interval  = new \DateInterval('P1M');
$daterange = new \DatePeriod($begin, $interval, $end);

foreach ($daterange as $date) {
    $ym_options[] = "<option " . ($date->format("Ym") == "{$year}{$month}" ? 'selected="selected"' : '') . ">{$date->format("Y-m")}</option>";
}
krsort($ym_options);

$content  = "
    <div class='row'>
        <div class='col-sm-1'><label>{$this->i18n('label.filter')}:</label></div>
        <div class='col-sm-2'>
            <div class='rex-select-style'><select name='year-month' class='form-control'>" . implode('', $ym_options) . "</select></div>
        </div>
        <div class='col-sm-2'>
            <div class='rex-select-style'><select name='status' class='form-control'>
                <option value=''>- {$this->i18n('label.all')} -</option>
                ". implode('', $_status_opts) ."
            </select></div>
        </div>
        <div class='col-sm-5'>
            <button class='btn btn-default float-left' type='submit' name='func' value='filter'>{$this->i18n('update')}</button>&nbsp;&nbsp;&nbsp;
            <button class='btn btn-apply float-left' type='submit' name='func' value='export'>" . \rex_i18n::msg('action.export') . "</button>
        </div>
        <div class='col-sm-2'>
        </div>
    </div>
";
$fragment = new \rex_fragment();
$fragment->setVar('body', $content, false);
$sections .= $fragment->parse('core/page/section.php');

$fragment = new \rex_fragment();
$fragment->setVar('Addon', $this);
$fragment->setVar('Settings', \rex::getConfig('simpleshop.Settings'));
$fragment->setVar('orders', $orders);
$fragment->setVar('order_ids', $order_ids);
$fragment->setVar('statuses', $statuses);
$content = $fragment->parse('simpleshop/backend/orders_export.php');

$fragment = new \rex_fragment();
$fragment->setVar('body', $content, false);
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', sprintf($this->i18n('label.orders_export'), count($orders)), false);
$sections .= $fragment->parse('core/page/section.php');

$formElements = [
    ['field' => '<button class="btn btn-apply rex-form-aligned" type="submit" name="func" value="export"' . \rex::getAccesskey(\rex_i18n::msg('action.export'), 'apply') . '>' . \rex_i18n::msg('action.export') . '</button>'],
];
$fragment     = new \rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new \rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('buttons', $buttons, false);
$sections .= $fragment->parse('core/page/section.php');

echo '<form action="' . \rex_url::currentBackendPage(['output' => $output]) . '" method="post">' . $sections . '</form>';