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

$buffer    = $this->getVar('buffer');
$output    = $this->getVar('output');
$order_ids = $this->getVar('order_ids');

$tax_vals = [];
$taxes    = Tax::query()->where('status', 1)->orderBy('tax')->find();
$orders   = Order::query()->where('id', $order_ids)->orderBy('createdate')->find();
$csv_head = ['Rechn.-Nr', 'ID', 'Jahr', 'Monat', 'Datum', 'Währung', 'Kunde', 'Land', 'Shop', 'Zahlart', 'Grundlage', 'Rabatt', 'Summe'];
$csv_body = [];

foreach ($taxes as $tax)
{
    $_id            = $tax->getValue('id');
    $tax_vals[$_id] = $tax->getValue('tax');
    $csv_head[]     = "Mwst.-Satz {$tax->getValue('tax')}%";
}

foreach ($orders as $order)
{
    $_taxes   = [];
    $date     = $order->getValue('createdate');
    $order_ts = strtotime($date);
    $order_id = $order->getValue('id');
    $Payment  = $order->getValue('payment');
    $Shipping = $order->getValue('shipping');
    $Customer = Customer::get($order->getValue('customer_id'));
    $products = OrderProduct::query()->where('order_id', $order_id)->find();
    $subtotal = $order->getValue('subtotal');
    $discount = $order->getValue('discount');
    $sh_tax   = $Shipping ? $Shipping->getValue('tax') : 0;


    $data = [
        '',
        $order_id,
        date('Y', $order_ts),
        date('F', $order_ts),
        $date,
        '€',
        $Customer->getName(),
        'Italien',
        \rex::getServerName(),
        $Payment ? $Payment->getName() : '',
        $order->getValue('subtotal') - $order->getValue('tax') + ($Shipping ? $Shipping->getValue('price') : 0),
        $discount >= $subtotal ? $subtotal : ($discount ?: ''),
        $order->getValue('total'),
    ];

    if ($Shipping && $sh_tax)
    {
        $sh_tax_p = $Shipping->getValue('tax_percentage');
        $tax_key  = array_search($sh_tax_p, $tax_vals);

        if ($tax_key === FALSE)
        {
            $tax_key            = '_' . $sh_tax_p;
            $tax_vals[$tax_key] = $sh_tax_p;
            $csv_head[]         = "Mwst.-Satz {$sh_tax_p}%";
        }
        $_taxes[$tax_key] += $Shipping->getValue('tax');
    }
    foreach ($products as $product)
    {
        $_data    = $product->getValue('data');
        $quantity = $product->getValue('quantity');
        $tax_id   = $_data->getValue('tax');
        $_taxes[$tax_id] += $_data->getValue('price') / 100 * ($tax_vals[$tax_id]) * $quantity;
    }
    foreach ($tax_vals as $tax_id => $tax)
    {
        $data[] = $_taxes[$tax_id] ?: '';
    }
    $csv_body[] = $data;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OUTPUT
if ($output == 'html')
{
    $html = [
        '<table border="1" cellpadding="5" cellspacing="0">',
        '<tr><th>' . implode('</th><th>', $csv_head) . '</th></tr>',
    ];
    foreach ($csv_body as $item)
    {
        $html[] = '<tr><td>' . implode('</td><td>', $item) . '</td></tr>';
    }
    $html[] = '</table>';
    echo implode('', $html);
}
else
{
    $buffer = fopen('php://output', 'w');
    // set headline
    fputcsv($buffer, $csv_head, ';');
    foreach ($csv_body as $item)
    {
        fputcsv($buffer, $item, ';');
    }
    fclose($buffer);
}
