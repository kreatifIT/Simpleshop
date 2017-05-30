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
$statuses  = $this->getVar('statuses');

$tax_vals = [];
$taxes    = Tax::query()->where('status', 1)->orderBy('tax')->find();
$orders   = Order::query()->where('id', $order_ids)->orderBy('createdate')->find();
$csv_head = [
    'Rechn.-Nr',
    'ID',
    'Jahr',
    'Monat',
    'Datum',
    'Währung',
    'Kunde',
    'Land',
    'Shop',
    'Zahlart',
    'Status',
    // 'Grundlage',
    'Gesamtbetrag',
    // 'Rabatt'
];
$csv_body = [];
$totals   = [
    // 'base' => 0,
    'sum' => 0,
    // 'discount' => 0
];

foreach ($taxes as $tax) {
    $_id            = $tax->getValue('id');
    $tax_vals[$_id] = $tax->getValue('tax');
    $csv_head[]     = "Imponibile {$tax->getValue('tax')}%";
    $csv_head[]     = "Totale {$tax->getValue('tax')}%";
}

foreach ($orders as $order) {
    $_taxes     = [];
    $date       = $order->getValue('createdate');
    $order_ts   = strtotime($date);
    $order_id   = $order->getValue('id');
    $status     = $order->getValue('status', false, '');
    $Payment    = $order->getValue('payment');
    $Shipping   = $order->getValue('shipping');
    $Address    = $order->getInvoiceAddress();
    $products   = OrderProduct::query()->where('order_id', $order_id)->find();
    $shipping   = $order->getValue('shipping_costs');
    $taxes      = $order->getValue('taxes');
    $subtotal   = $order->getValue('subtotal');
    $discount   = $order->getValue('discount') >= $subtotal ? $subtotal : (abs($order->getValue('discount')) ?: '');
    $net_prices = $order->getValue('net_prices', false, []);
    // $base_price  = $order->getValue('subtotal') - $order->getValue('tax') + ($Shipping ? $Shipping->getValue('price') : 0);
    $total_price = $order->getValue('total');

    if ($Shipping && $shipping) {
        $tax_perc = $Shipping->getValue('tax_percentage');

        if (!in_array($tax_perc, $tax_vals)) {
            $tax_vals[] = $tax_perc;
            $csv_head[] = "Imponibile {$tax_perc}%";
            $csv_head[] = "Totale {$tax_perc}%";
        }
        $subtotal += $shipping;
        $net_prices[$tax_perc] += $shipping;
        $_taxes[$tax_perc] += $shipping + ($shipping / 100 * $tax_perc);
    }

    if (is_array($taxes)) {
        $_taxes = $taxes;
    }
    else {
        foreach ($products as $product) {
            $_Product = $product->getValue('data');
            $quantity = $product->getValue('quantity');
            $tax_val  = Tax::get($_Product->getValue('tax'))->getValue('tax');
            $_taxes[$tax_val] += $_Product->getTax() * $quantity;
            $net_prices[$tax_val] += $_Product->getPrice(false) * $quantity;
        }
        if ($discount) {
            $net_prices[$tax_val] -= $discount / (100 + $tax_val) * 100;
            $_taxes[$tax_val] = $net_prices[$tax_val] / 100 * $tax_val;
        }
    }

    $data = [
        $order->getValue('invoice_num', false, ''),
        $order_id,
        date('Y', $order_ts),
        date('m', $order_ts),
        $date,
        '€',
        $Address->getName(),
        $Address->getValue('country'),
        \rex::getServerName(),
        $Payment ? $Payment->getName() : '',
        $statuses[$status] ?: $status,
        // format_price($subtotal),
        format_price($total_price),
        // $discount,
    ];

    // $totals['base'] += $subtotal;
    // $totals['discount'] += $discount;
    $totals['sum'] += $total_price;

    foreach ($tax_vals as $tax) {
        $data[] = $net_prices[$tax] ? format_price($net_prices[$tax]) : '';
        $data[] = $_taxes[$tax] ? format_price($net_prices[$tax] + $_taxes[$tax]) : '';
        $totals['imp_tax_' . $tax] += number_format($net_prices[$tax], 2);
        $totals['tax_' . $tax] += number_format($net_prices[$tax] + $_taxes[$tax], 2);
    }
    $csv_body[] = $data;
}
$csv_body[] = array_merge(['', '', '', '', '', '', '', '', '', '', ''], $totals);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OUTPUT
if ($output == 'html') {
    $html = [
        '<table border="1" cellpadding="5" cellspacing="0">',
        '<tr><th>' . implode('</th><th>', $csv_head) . '</th></tr>',
    ];
    foreach ($csv_body as $item) {
        $html[] = '<tr><td>' . implode('</td><td>', $item) . '</td></tr>';
    }
    $html[] = '</table>';
    echo implode('', $html);
}
else {
    $buffer = fopen('php://output', 'w');
    // set headline
    fputcsv($buffer, $csv_head, ';');
    foreach ($csv_body as $item) {
        fputcsv($buffer, $item, ';');
    }
    fclose($buffer);
}
