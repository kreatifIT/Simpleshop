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

class Order extends Model
{
    const TABLE = 'rex_shop_order';

    private static $_finalizeOrder = false;

    public static function create($table = null)
    {
        $_this = parent::create($table);

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.getObjectData', $_this->object_data));

        return $_this;
    }

    public function getShippingAddress()
    {
        return $this->getValue('shipping_address') == '' ? $this->getValue('invoice_address') : $this->getValue('shipping_address');
    }

    public function getInvoiceAddress()
    {
        return $this->getValue('invoice_address');
    }

    public function isTaxFree()
    {
        $Customer = $this->getValue('customer_data');
        $Country  = null;

        if ($Customer->isCompany()) {
            $Address = $this->getInvoiceAddress();
            $Country = Country::get($Address->getValue('country'));
        }

        return $Country && !$Country->getValue('b2b_has_tax');
    }

    public function getProducts($raw = true)
    {
        $products = OrderProduct::getAll(false, [
            'filter'  => [['order_id', $this->getId()]],
            'orderBy' => 'id',
        ])
            ->toArray();


        if (!$raw) {
            if (empty($products)) {
                $products = $this->getValue('products');
            } else {
                foreach ($products as $index => &$orderProduct) {
                    $Product     = clone $orderProduct->getValue('data');
                    $orderPData  = $orderProduct->getData();
                    $productData = $Product->getData();

                    foreach ($orderPData as $key => $value) {
                        if (!array_key_exists($key, $productData)) {
                            $Product->setValue($key, $value);
                        }
                    }
                    $orderProduct = $Product;
                }
            }
        }
        return $products;
    }

    public function getInvoiceNum()
    {
        $invoice_num = trim($this->getValue('invoice_num'));

        if ($invoice_num == '0' || $invoice_num == '' || $invoice_num == 0) {
            $invoice_num = null;
        }

        return \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.getInvoiceNum', $invoice_num, [
            'Order' => $this,
        ]));
    }

    public function getReferenceId()
    {
        $_refId = date('Ym', strtotime($this->getValue('createdate'))) . str_pad($this->getId(), 6, '0', STR_PAD_LEFT);

        return \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.getReferenceId', $_refId, [
            'Order' => $this,
        ]));
    }

    public function getShippingKey($forBarcode = false)
    {
        $refId = $this->getReferenceId();
        return \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.getShippingKey', $refId, [
            'Order'      => $this,
            'forBarcode' => $forBarcode,
        ]));
    }

    public function getBarCodeImg()
    {
        $img = null;
        $key = $this->getShippingKey(true);

        if (strlen($key) == 12 || strlen($key) == 13) {
            $Code = new \Barcode($key, 4);

            ob_start();
            imagepng($Code->image());
            $img = 'data:image/png;base64,' . base64_encode(ob_get_clean());
        }
        return $img;
    }

    public function completeOrder()
    {
        self::$_finalizeOrder = true;

        $result = $this->save(false);

        return \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.completeOrder', $result, [
            'Order' => $this,
        ]));
    }

    public function save($simple_save = true)
    {
        Utils::setCalcLocale();

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.preSave', $this, ['finalize_order' => self::$_finalizeOrder, 'simple_save' => $simple_save]));

        $sql      = \rex_sql::factory();
        $date_now = date('Y-m-d H:i:s');
        $products = $this->getProducts(false);

        if (!$this->valueIsset('createdate')) {
            $this->setValue('createdate', $date_now);
        }

        if (self::$_finalizeOrder && ($this->getValue('invoice_num') === null || $this->getValue('invoice_num') === 0)) {
            $sql->setQuery('SELECT MAX(invoice_num) as num FROM ' . Order::TABLE . ' WHERE createdate >= "' . date('Y-01-01 00:00:00') . '"');
            $value = $sql->getValue('num');
            $num   = (int)substr($value, 2) + 1;
            $num   = date('y') . str_pad($num, 5, '0', STR_PAD_LEFT);

            $this->setValue('invoice_num', \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.invoice_num', $num, ['Order' => $this, 'value' => $value])));
        }

        $result = parent::save(true);
        if (!$result) {
            throw new OrderException(implode('<br/>', $this->getMessages()));
        }


        if ($result && !$simple_save) {
            $order_id   = $this->getId();
            $promotions = $this->getValue('promotions');

            // IMPORTANT! after saving readd the products
            $this->setValue('products', $products);

            if (self::$_finalizeOrder && isset ($promotions['coupon'])) {
                // relate coupon
                $promotions['coupon']->linkToOrder($this);
            }

            // reset quanities
            $order_products = OrderProduct::getAll(false, [
                'filter'  => [['order_id', $order_id]],
                'orderBy' => 'id',
            ]);

            foreach ($order_products as $order_product) {
                if ($order_product->getValue('cart_quantity')) {
                    $_product = $order_product->getValue('data');
                    $quantity = $order_product->getValue('cart_quantity');

                    if ($_product->getValue('inventory') == 'F') {
                        // update inventory
                        if ($_product->getValue('variant_key')) {
                            $Variant = Variant::getByVariantKey($_product->getValue('key'));
                            $Variant->setValue('amount', $Variant->getValue('amount') + $quantity);
                            $Variant->save();
                        } else {
                            $product = Product::get($_product->getId());
                            $product->setValue('amount', $product->getValue('amount') + $quantity);
                            $product->save();
                        }
                    }
                }
            }

            // clear all products first
            \rex_sql::factory()
                ->setQuery("DELETE FROM " . OrderProduct::TABLE . " WHERE order_id = {$order_id}");

            // set order products
            foreach ($products as $product) {
                $prod_data = Model::prepare($product);
                $quantity  = $product->getValue('cart_quantity');

                foreach ($prod_data as $name => $value) {
                    $product->setValue($name, $value);
                }
                if (self::$_finalizeOrder) {
                    if ($product->getValue('inventory') == 'F') {
                        // update inventory
                        if ($product->getValue('variant_key')) {
                            $Variant = Variant::getByVariantKey($product->getValue('key'));
                            $Variant->setValue('amount', $Variant->getValue('amount') - $quantity);
                            $Variant->save();
                        } else {
                            $product->setValue('amount', $product->getValue('amount') - $quantity);
                            $product->save();
                        }
                    }
                    if ($product->getValue('type') == 'giftcard') {
                        Coupon::createGiftcard($this, $product);
                    }
                }

                $OrderProduct = OrderProduct::create();
                $OrderProduct->setValue('data', $product);
                $OrderProduct->setValue('product_id', $product->getValue('id'));
                $OrderProduct->setValue('code', $product->getValue('code'));
                $OrderProduct->setValue('cart_quantity', $quantity);
                $OrderProduct->setValue('order_id', $order_id);
                $OrderProduct->setValue('createdate', $date_now);

                $OrderProduct = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.saveOrderProduct', $OrderProduct, [
                    'Order' => $this,
                ]));

                $OrderProduct->save(true);
            }
        }
        Utils::resetLocale();
        return $result;
    }

    public function recalculateDocument($products = [], $promotions = [], $errors = [])
    {
        Utils::setCalcLocale();

        $net_prices          = [];
        $this->quantity      = 0;
        $this->discount      = 0;
        $this->initial_total = 0;
        $manual_discount     = $this->getValue('manual_discount', false, 0);

        if ($this->getValue('status') == 'CN') {
            // calculate credit note total
            $ROrder = Order::get($this->getValue('ref_order_id'));

            if ($ROrder) {
                $net_prices = $this->calculateCreditNote($ROrder);
            }
        } else {
            // calculate products total
            foreach ($products as $product) {
                $quantity = $product->getValue('cart_quantity');
                $tax_perc = $this->isTaxFree() ? 0 : Tax::get($product->getValue('tax'))
                    ->getValue('tax');

                $net_prices[$tax_perc] += (float)$product->getPrice(!$this->isTaxFree()) * $quantity;
                $this->initial_total   += (float)$product->getPrice(!$this->isTaxFree()) * $quantity;
                $this->quantity        += $quantity;
            }
        }
        // get shipping costs
        if ($this->getValue('shipping')) {
            try {
                $this->setValue('shipping_costs', (float)$this->getValue('shipping')
                    ->getNetPrice($this, $products));
            } catch (\Exception $ex) {
                $msg = trim($ex->getLabelByCode());

                if ($msg == '') {
                    $msg = $ex->getMessage();
                }
                throw new OrderException($msg);
            }
        } else {
            $this->setValue('shipping_costs', 0);
        }
        ksort($net_prices);

        $this->setValue('updatedate', date('Y-m-d H:i:s'));
        $this->setValue('net_prices', $net_prices);
        $this->setValue('total', array_sum($net_prices));

        // calculate manual discount8
        if ($manual_discount > 0) {
            $mDiscount = DiscountGroup::create();
            $mDiscount->setValue('discount_value', $manual_discount);
            $mDiscount->setValue('ctype', 'all');
            $mDiscount->setValue('name_' . \rex_clang::getCurrentId(), '###label.discount###');

            $promotions['manual_discount'] = $mDiscount;
        }

        // set promotions for order history
        list($_errors, $promotions) = $this->calculatePrices($promotions, $net_prices);

        $errors = array_merge((array)$errors, (array)$_errors);

        $this->setValue('promotions', $promotions);
        $this->setValue('products', $products);

        try {
            \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.calculateDocument', $this));
        } catch (\Exception $ex) {
            $errors[] = ['label' => $ex->getLabelByCode()];
        }

        // re-check order totals
        if ($this->total < 0) {
            $this->setValue('total', $this->total);
        }

        Utils::resetLocale();

        return $errors;
    }

    public function calculateCreditNote(Order $ReferenceOrder)
    {
        $total      = $ReferenceOrder->getValue('total') * -1;
        $net_prices = $ReferenceOrder->getValue('net_prices');

        foreach ($net_prices as &$net_price) {
            $net_price = $net_price * -1;
        }

        $this->setValue('customer_id', $ReferenceOrder->getValue('customer_id'));
        $this->setValue('status', 'CN');
        $this->setValue('initial_total', $total);
        $this->setValue('net_prices', $net_prices);
        $this->setValue('address_1', $ReferenceOrder->getValue('address_1'));
        $this->setValue('ip_address', rex_server('REMOTE_ADDR', 'string', 'notset'));
        $this->setValue('total', $total);
        $this->setValue('ref_order_id', $ReferenceOrder->getId());

        self::$_finalizeOrder = true;

        return $net_prices;
    }

    private function calculatePrices($promotions, $net_prices)
    {
        $taxes       = [];
        $errors      = [];
        $_promotions = [];
        $brut_prices = [];
        $discount    = 0;

        $this->setValue('net_prices', $net_prices);

        // set gross prices
        foreach ($net_prices as $tax => $net_price) {
            $brut_prices[$tax] += (float)$net_price / (100 + $tax) * 100;
        }

        $this->setValue('brut_prices', $brut_prices);

        foreach ($promotions as $name => $promotion) {
            try {
                if (is_object($promotion)) {
                    $discount += $promotion->applyToOrder($this, $brut_prices, $name);
                }
            } catch (\Exception $ex) {
                $errors[]  = ['label' => $ex->getLabelByCode()];
                $promotion = null;
            }
            if ($promotion) {
                $_promotions[$name] = $promotion;
            }
        }

        // set tax costs
        foreach ($brut_prices as $tax => $brut_price) {
            $taxes[$tax] += (float)$brut_price * ($tax / 100);
        }

        // set shipping costs
        if ($this->getValue('shipping_costs') > 0 && !$this->isTaxFree()) {
            $tax = $this->getValue('shipping')
                ->getTaxPercentage();

            if ($tax > 0) {
                $this->setValue('shipping_costs', (float)$this->getValue('shipping_costs') / (100 + $tax) * 100);
                $taxes[$tax] += $this->getValue('shipping')
                    ->getTax();
            }
        }
        ksort($taxes);

        $this->setValue('taxes', $taxes);
        $this->setValue('discount', $discount);
        $this->setValue('total', $this->getValue('shipping_costs') + array_sum($brut_prices) + array_sum($taxes)) - $discount;

        return [$errors, $_promotions];
    }

    public function calculateDocument(CheckoutController $caller) // $caller is used to prevent unauthorized method calls
    {
        try {
            $products = Session::getCartItems();
        } catch (CartException $ex) {
            if ($ex->getCode() == 1) {
                $errors   = Session::$errors;
                $products = Session::getCartItems();
            }
        }

        try {
            $promotions = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.applyDiscounts', [], [
                'Order'    => $this,
                'products' => $products,
            ]));
        } catch (\Exception $ex) {
            $promotions = [];
            $errors[]   = ['label' => $ex->getLabelByCode()];
        }

        $this->setValue('initial_total', 0);
        $this->setValue('quantity', 0);
        $this->setValue('ip_address', rex_server('REMOTE_ADDR', 'string', 'notset'));

        return $this->recalculateDocument($products, $promotions, $errors);
    }

    public static function ext_yform_data_delete($params)
    {
        $result = $params->getSubject();

        if ($result !== false && $params->getParam('table')
                ->getTableName() == self::TABLE
        ) {
            // remove all related order products
            $obj_id = $params->getParam('data_id');
            $query  = "DELETE FROM " . OrderProduct::TABLE . " WHERE order_id = {$obj_id}";
            $sql    = \rex_sql::factory();
            $sql->setQuery($query);
        }
        return $result;
    }

    public function getInvoicePDF($type = 'invoice', $debug = false, Mpdf $_Mpdf = null)
    {
        $content    = '';
        $fragment   = new \rex_fragment();
        $Mpdf       = $_Mpdf ?: new \Kreatif\Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 10,
            'margin_bottom' => 34,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $invoiceNum = $this->getInvoiceNum();

        if ($invoiceNum == null) {
            $type = 'order';
        }

        $Mpdf->SetProtection(['print']);
        $Mpdf->SetDisplayMode('fullpage');

        $fragment->setVar('type', $type);
        $fragment->setVar('debug', $debug);
        $fragment->setVar('Customer', $this->getInvoiceAddress());
        $fragment->setVar('Order', $this);

        // HEADER
        $content .= $fragment->parse('simpleshop/pdf/invoice/header.php');
        // INVOICE DATA
        $content .= $fragment->parse('simpleshop/pdf/invoice/invoice_data.php');
        // ITEMS
        $content .= $fragment->parse('simpleshop/pdf/invoice/items.php');
        // SUMMARY
        $content .= $fragment->parse('simpleshop/pdf/invoice/summary.php');

        $docTitle = $type == 'invoice' ? 'simpleshop.invoice_title' : 'simpleshop.orderdocument_title';
        $fragment->setVar('title', strtr(\Wildcard::get($docTitle), ['{NUM}' => $invoiceNum]));
        $fragment->setVar('content', $content, false);
        $html = $fragment->parse('simpleshop/pdf/invoice/wrapper.php');

        if ($debug) {
            echo "<div style='background:#666;height:100vh;padding:20px;'><div style='max-width:800px;margin:0px auto;background:#fff;'>{$html}</div></div>";
            exit;
        }
        $Mpdf->WriteHTML($html);

        return $Mpdf;
    }

    public function getPackingListPDF($debug = false)
    {
        $Customer = $this->getValue('customer_data');

        if ($Customer->valueIsset('lang_id')) {
            \Kreatif\Utils::setCLang($Customer->getValue('lang_id'));
        }

        $content  = '';
        $refId    = $this->getReferenceId();
        $fragment = new \rex_fragment();
        $Mpdf     = new \Kreatif\Mpdf\Mpdf([
            'orientation'      => 'L',
            'margin_left'      => 20,
            'margin_right'     => 15,
            'margin_top'       => 5,
            'margin_bottom'    => 5,
            'margin_header'    => 10,
            'margin_footer'    => 0,
            'setAutoTopMargin' => 'pad',
        ]);

        $Mpdf->SetProtection(['print']);
        $Mpdf->SetDisplayMode('fullpage');

        $fragment->setVar('debug', $debug);
        $fragment->setVar('Order', $this);

        // HEADER
        $Mpdf->DefHTMLHeaderByName('PackingListHeader', \Wildcard::parse($fragment->parse('simpleshop/pdf/packing_list/header.php')));
        $Mpdf->SetHTMLHeaderByName('PackingListHeader');
        // ITEMS
        $content .= $fragment->parse('simpleshop/pdf/packing_list/items.php');

        $fragment->setVar('title', strtr(\Wildcard::parse(\Wildcard::get('simpleshop.packing_list_title')), ['{NUM}' => $refId]));
        $fragment->setVar('content', $content, false);
        $html = $fragment->parse('simpleshop/pdf/invoice/wrapper.php');

        if ($debug) {
            echo "<div style='background:#666;height:100vh;padding:20px;'><div style='max-width:800px;margin:0px auto;background:#fff;'>{$html}</div></div>";
            exit;
        }
        $Mpdf->WriteHTML($html);

        return $Mpdf;
    }
}

class OrderException extends \Exception
{
}