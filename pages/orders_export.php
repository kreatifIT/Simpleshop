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

use Kreatif\Project\Settings;


echo \rex_view::title('Simpleshop');

$_FUNC     = rex_post('func', 'string');
$order_ids = rex_post('orders', 'array', []);
$output    = rex_request('output', 'string', 'file');
$status    = rex_request('status', 'string');

list ($year, $month) = explode('-', rex_request('year-month', 'string', date('Y-m')));

$statuses     = [];
$_status_opts = [];
$_options     = explode(',', \rex_yform_manager_table::get(Order::TABLE)
    ->getValueField('status')
    ->getElement('options'));

foreach ($_options as $option) {
    list ($value, $key) = explode('=', $option);
    $statuses[trim($key)] = trim($value);
    $_status_opts[]       = '<option value="' . $key . '" ' . ($key == $status ? 'selected="selected"' : '') . '>' . trim($value) . '</option>';
}

$_FUNC = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.orders_export.export', $_FUNC, [
    'month'     => $month,
    'year'      => $year,
    'order_ids' => $order_ids,
    'statuses'  => $statuses,
    'output'    => $output,
]));

if ($_FUNC == 'export-csv' && count($order_ids)) {
    ob_clean();

    if ($output == 'file') {
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment;filename=orders-{$year}-{$month}.csv");
    } else {
        header('Content-Type: text/html; charset=utf-8');
    }
    $fragment = new \rex_fragment();
    $fragment->setVar('order_ids', $order_ids);
    $fragment->setVar('output', $output);
    $fragment->setVar('statuses', $statuses);
    echo $fragment->parse('simpleshop/backend/export/orders_export_csv.php');
    exit;
} else if ($_FUNC == 'export-xml' && count($order_ids)) {
    $zip         = new \ZipArchive();
    $folder      = \rex_path::addonData('simpleshop', 'invoice_xml/' . date('Y') . '/' . date('m'));
    $archiveName = date('Y-m-d_His') . '.zip';

    \rex_response::cleanOutputBuffers();
    \rex_dir::create($folder . '/' . date('Ymd'), true);

    $zip->open($folder . '/' . $archiveName, \ZipArchive::CREATE);

    foreach ($order_ids as $order_id) {
        $Order = Order::get($order_id);
        $XMLi  = $Order->getXML();

        if ($XMLi) {
            $XMLi->buildXML();
            $xml   = $XMLi->getXMLFormated();
            $iDate = date('Y-m-d', strtotime($Order->getValue('createdate')));

            $filename = \rex::getServerName() . '_' . $Order->getValue('id') . '_' . $iDate . '__' . $Order->getValue('invoice_num') . '.xml';
            \rex_file::put($folder . '/' . date('Ymd') . '/' . $filename, \Wildcard::parse($xml));

            $zip->addFile($folder . '/' . date('Ymd') . '/' . $filename, $filename);
        }
    }
    $zip->close();

    \rex_dir::deleteFiles($folder . '/' . date('Ymd') . '/');
    \rex_dir::delete($folder . '/' . date('Ymd') . '/');
    \rex_response::sendCacheControl();
    \rex_response::sendFile($folder . '/' . $archiveName, 'application/zip', 'attachment');
    exit;
} else if ($_FUNC == 'export-pdf' && count($order_ids)) {
    ob_clean();

    if ($output == 'file') {
        header('Content-Type: application/pdf; charset=utf-8');
        header("Content-Disposition: attachment;filename=orders-{$year}-{$month}.pdf");
        header('Content-Description: File Transfer');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');
        $tpl = 'orders_export_pdf.php';
    } else {
        header('Content-Type: text/html; charset=utf-8');
        $tpl = 'orders_export_csv.php';
    }
    $fragment = new \rex_fragment();
    $fragment->setVar('order_ids', $order_ids);
    $fragment->setVar('output', $output);
    $fragment->setVar('statuses', $statuses);
    echo $fragment->parse('simpleshop/backend/export/' . $tpl);
    exit;
}

$orders = Order::query()
    ->where('createdate', "{$year}-{$month}-01", '>=')
    ->where('createdate', date('Y-m-d 23:59:59', strtotime("{$year}-{$month} next month -1 day")), '<=')
    ->orderBy('id');

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

$fragment = new \rex_fragment();
$fragment->setVar('status_options', $_status_opts, false);
$fragment->setVar('months', $ym_options, false);
$content = $fragment->parse('simpleshop/backend/export/action_bar.php');

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