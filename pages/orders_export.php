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

if ($_FUNC == 'export' && count($order_ids))
{
    ob_clean();

    if ($output == 'file')
    {
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment;filename=orders-{$year}-{$month}.csv");
    }
    $fragment = new \rex_fragment();
    $fragment->setVar('order_ids', $order_ids);
    $fragment->setVar('output', $output);
    echo $fragment->parse('simpleshop/backend/export/orders_export.php');
    exit;
}

$orders = Order::query()
    ->where('createdate', "{$year}-{$month}-01", '>=')
    ->where('createdate', date('Y-m-d', strtotime("{$year}-{$month} next month -1 day")), '<=')
    ->orderBy('id');

if ($status != '')
{
    $orders->where('status', $status);
}
$orders = $orders->find();


$sections   = '';
$ym_options = [];

$sql = \rex_sql::factory();
$sql->setQuery("SELECT CONCAT(YEAR(MIN(createdate)), MONTH(MIN(createdate))) AS min_date FROM " . Order::TABLE);
$_month = range($sql->getValue('min_date'), date('Ym'));
rsort($_month);

foreach ($_month as $m)
{
    $_y           = substr($m, 0, 4);
    $_m           = substr($m, 4);
    $ym_options[] = "<option " . ($m == "{$year}{$month}" ? 'selected="selected"' : '') . ">{$_y}-{$_m}</option>";
}

$content  = "
    <div class='row'>
        <div class='col-sm-1'><label>{$this->i18n('label.filter')}:</label></div>
        <div class='col-sm-2'>
            <div class='rex-select-style'><select name='year-month' class='form-control'>" . implode('', $ym_options) . "</select></div>
        </div>
        <div class='col-sm-2'>
            <div class='rex-select-style'><select name='status' class='form-control'>
                <option value=''>- {$this->i18n('label.all')} -</option>
                <option value='OP' ". ($status == 'OP' ? 'selected="selected"' : '') .">auf Zahlung wartend</option>
                <option value='IP' ". ($status == 'IP' ? 'selected="selected"' : '') .">in Bearbeitung</option>
                <option value='FA' ". ($status == 'FA' ? 'selected="selected"' : '') .">Fehlgeschlagen</option>
                <option value='SH' ". ($status == 'SH' ? 'selected="selected"' : '') .">Versendet</option>
                <option value='CA' ". ($status == 'CA' ? 'selected="selected"' : '') .">Storniert</option>
                <option value='CL' ". ($status == 'CL' ? 'selected="selected"' : '') .">Abgeschlossen</option>
            </select></div>
        </div>
        <div class='col-sm-3'>
            <button class='btn btn-apply' type='submit' name='func' value='filter'>{$this->i18n('update')}</button>
        </div>
        <div class='col-sm-4'>
        </div>
    </div>
";
$fragment = new \rex_fragment();
$fragment->setVar('body', $content, FALSE);
$sections .= $fragment->parse('core/page/section.php');

$fragment = new \rex_fragment();
$fragment->setVar('Addon', $this);
$fragment->setVar('orders', $orders);
$fragment->setVar('order_ids', $order_ids);
$content = $fragment->parse('simpleshop/backend/orders_export.php');

$fragment = new \rex_fragment();
$fragment->setVar('body', $content, FALSE);
$fragment->setVar('class', 'edit', FALSE);
$fragment->setVar('title', sprintf($this->i18n('label.orders_export'), count($orders)), FALSE);
$sections .= $fragment->parse('core/page/section.php');

$formElements = [
    ['field' => '<button class="btn btn-apply rex-form-aligned" type="submit" name="func" value="export"' . \rex::getAccesskey(\rex_i18n::msg('action.export'), 'apply') . '>' . \rex_i18n::msg('action.export') . '</button>'],
];
$fragment     = new \rex_fragment();
$fragment->setVar('elements', $formElements, FALSE);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new \rex_fragment();
$fragment->setVar('class', 'edit', FALSE);
$fragment->setVar('buttons', $buttons, FALSE);
$sections .= $fragment->parse('core/page/section.php');

echo '<form action="' . \rex_url::currentBackendPage(['output' => $output]) . '" method="post">' . $sections . '</form>';