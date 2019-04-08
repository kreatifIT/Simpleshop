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


use Kreatif\Mpdf\Mpdf;
use Kreatif\Project\Settings;


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
        return $this->valueIsset('address_2') ? $this->getValue('address_2') : $this->getValue('address_1');
    }

    public function getInvoiceAddress()
    {
        return $this->getValue('address_1');
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

    public function isTaxFree()
    {
        // add country + company tax check
        return false;
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

    public function getTaxTotal()
    {
        return array_sum($this->getValue('taxes', false, [])) ?: array_sum($this->getValue('tax'));
    }

    public function getNetPrice()
    {
        return $this->getValue('initial_total', false, 0);
    }

    public function getGrossPrice()
    {
        return $this->getValue('total', false, 0);
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

        if (!$this->valueIsset('createdate')) {
            $this->setValue('createdate', $date_now);
        }

        if (self::$_finalizeOrder && ($this->getValue('invoice_num') === null || (int)$this->getValue('invoice_num') === 0)) {
            $query = 'SELECT MAX(invoice_num) + 1 as num FROM ' . Order::TABLE . ' WHERE createdate >= "' . date('Y-01-01 00:00:00') . '"';
            $sql->setQuery($query);
            $num = $sql->getValue('num');
            $num = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.invoice_num', $num, ['Order' => $this]));

            if ($num == 0) {
                $num = null;
            }
            $this->setValue('invoice_num', $num);
        }

        $result = parent::save(true);

        if ($result && !$simple_save) {

            $order_id   = $this->getId();
            $promotions = $this->getValue('promotions');

            if (self::$_finalizeOrder && isset ($promotions['coupon'])) {
                // relate coupon
                $promotions['coupon']->linkToOrder($this);
            }

            // reset quanities
            $order_products = OrderProduct::getAll(false, ['filter' => [['order_id', $order_id]], 'orderBy' => 'id']);

            foreach ($order_products as $order_product) {
                if ($order_product->getValue('quantity')) {
                    $_product = $order_product->getValue('data');
                    $quantity = $order_product->getValue('quantity');

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

            $products = Session::getCartItems(false, false, false);

            // set order products
            foreach ($products as $product) {
                $prod_data = Model::prepare($product);
                $quantity  = $product->getValue('cart_quantity');

                if (!$quantity) {
                    $quantity = $product->getValue('quantity');
                }

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
                $OrderProduct->setValue('order_id', $order_id . 'a');
                $OrderProduct->setValue('product_id', $product->getValue('id'));
                $OrderProduct->setValue('code', $product->getValue('code'));
                $OrderProduct->setValue('quantity', $quantity);
                $OrderProduct->setValue('data', $product);
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

        $gross_prices    = [];
        $net_prices      = [];
        $this->quantity  = 0;
        $this->discount  = 0;
        $manual_discount = $this->getValue('manual_discount', false, 0);

        if ($this->getValue('status') == 'CN') {
            // calculate credit note total
            $ROrder = Order::get($this->getValue('ref_order_id'));

            if ($ROrder) {
                $gross_prices = $this->calculateCreditNote($ROrder);
            }
        } else {
            // calculate products total
            foreach ($products as $product) {
                $quantity = $product->getValue('cart_quantity');

                if (!$quantity) {
                    $quantity = $product->getValue('quantity');
                }
                $tax_perc = $this->isTaxFree() ? 0 : Tax::get($product->getValue('tax'))
                    ->getValue('tax');

                $net_prices[$tax_perc]   += (float)$product->getPrice(false) * $quantity;
                $gross_prices[$tax_perc] += (float)$product->getPrice(!$this->isTaxFree()) * $quantity;
                $this->quantity          += $quantity;
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
        ksort($gross_prices);
        ksort($net_prices);


        $this->setValue('updatedate', date('Y-m-d H:i:s'));
        $this->setValue('net_prices', $net_prices);
        $this->setValue('brut_prices', $gross_prices);
        $this->setValue('initial_total', array_sum($gross_prices));
        $this->setValue('total', array_sum($gross_prices));

        // calculate manual discount8
        if ($manual_discount > 0) {
            $mDiscount = DiscountGroup::create();
            $mDiscount->setValue('discount_value', $manual_discount);
            $mDiscount->setValue('ctype', 'all');
            $mDiscount->setValue('name_' . \rex_clang::getCurrentId(), '###label.discount###');

            $promotions['manual_discount'] = $mDiscount;
        }

        // set promotions for order history
        list($_errors, $promotions) = $this->calculatePrices($promotions);

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
        $gross_prices = $ReferenceOrder->getValue('brut_prices');
        $net_prices   = $ReferenceOrder->getValue('net_prices');
        $total        = $ReferenceOrder->getValue('total') * -1;

        $this->setValue('customer_id', $ReferenceOrder->getValue('customer_id'));
        $this->setValue('status', 'CN');
        $this->setValue('initial_total', $total);
        $this->setValue('total', $total);
        $this->setValue('brut_prices', $gross_prices);
        $this->setValue('net_prices', $net_prices);
        $this->setValue('address_1', $ReferenceOrder->getValue('address_1'));
        $this->setValue('ip_address', rex_server('REMOTE_ADDR', 'string', 'notset'));
        $this->setValue('ref_order_id', $ReferenceOrder->getId());

        self::$_finalizeOrder = true;

        return $gross_prices;
    }

    private function calculatePrices($promotions)
    {
        $discount     = 0;
        $taxes        = [];
        $errors       = [];
        $_promotions  = [];
        $gross_prices = $this->getValue('brut_prices');

        foreach ($promotions as $name => $promotion) {
            try {
                if (is_object($promotion)) {
                    $discount += $promotion->applyToOrder($this, $gross_prices, $name);
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
        foreach ($gross_prices as $tax => $gross_price) {
            $taxes[$tax] += (float)$gross_price / ($tax + 100) * $tax;
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
        $this->setValue('brut_prices', $gross_prices);
        $this->setValue('total', $this->getValue('shipping_costs') + array_sum($gross_prices));

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
            $promotions = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.applyDiscounts', [$this->getValue('discount')], ['Order' => $this]));
        } catch (\Exception $ex) {
            $promotions = [];
            $errors[]   = ['label' => $ex->getLabelByCode()];
        }

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
        if (!class_exists('Kreatif\Mpdf\Mpdf')) {
            return false;
        }

        $content    = '';
        $fragment   = new \rex_fragment();
        $Mpdf       = $_Mpdf ?: new Mpdf([]);
        $invoiceNum = $this->getInvoiceNum();

        if ($invoiceNum == null) {
            $type = 'order';
        }

        $discounts = [];
        $docTitle  = $type == 'invoice' ? 'simpleshop.invoice_title' : 'simpleshop.orderdocument_title';
        $mdiscount = $this->getValue('manual_discount');

        $Mpdf->SetProtection(['print']);
        $Mpdf->SetDisplayMode('fullpage');

        $fragment->setVar('Customer', $this->getInvoiceAddress());
        $fragment->setVar('Order', $this);
        $fragment->setVar('type', $type);
        $content .= $fragment->parse('simpleshop/pdf/invoice/header.php');

        $content .= $fragment->parse('simpleshop/pdf/invoice/invoice_data.php');

        $content .= $fragment->parse('simpleshop/pdf/invoice/items.php');


        if (count($discounts) == 0 && $this->getValue('discount') > 0) {
            $discounts[] = ['name' => '###label.discount###', 'value' => $this->getValue('discount')];
        }
        if ($mdiscount) {
            $discounts[] = ['name' => '###label.discount###', 'value' => $mdiscount];
        }
        $fragment->setVar('discounts', $discounts);
        $fragment->setVar('tax', $this->getTaxTotal());
        $fragment->setVar('old_tax', $this->getValue('tax'));
        $fragment->setVar('taxes', $this->getValue('taxes'));
        $fragment->setVar('total', $this->getValue('total'));
        $fragment->setVar('initial_total', $this->getValue('initial_total'));
        $content .= $fragment->parse('simpleshop/pdf/invoice/summary.php');

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

    public function getXML()
    {
        if ((int)$this->getValue('invoice_num') <= 0) {
            return null;
        }

        $XMLInvoice  = XMLInvoice::factory();
        $Customer    = $this->getInvoiceAddress();
        $xmlData     = $XMLInvoice->getData();
        $iDateTs     = strtotime($this->getValue('createdate'));
        $iDate       = date('Y-m-d', $iDateTs);
        $totalnetto  = number_format(abs($this->getValue('total')) / 122 * 100, 2, '.', '');
        $totalbrutto = number_format($totalnetto * 1.22, 2, '.', '');

        $xmlData["document_lines"]                 = [];
        $xmlData['document_type']                  = $this->getValue('status') == 'CN' ? 'TD04' : 'TD01';
        $xmlData['receiver_name']                  = trim($Customer->getName());
        $xmlData['receiver_private_vat_number']    = mb_strtoupper($Customer->getValue('fiscal_code'));
        $xmlData['receiver_head_quarter_street']   = $Customer->getValue('street');
        $xmlData['receiver_head_quarter_zip']      = $Customer->getValue('postal');
        $xmlData['receiver_head_quarter_city']     = $Customer->getValue('location');
        $xmlData['receiver_head_quarter_province'] = 'BZ';
        $xmlData['receiver_head_quarter_nation']   = 'IT';

        $xmlData['document_date']   = $iDate;
        $xmlData['document_number'] = $this->getValue('invoice_num');

        $xmlData["document_lines"][1]["line_number"]         = 1;
        $xmlData["document_lines"][1]["line_description"]    = Settings::INVOICE_LINE_DESC;
        $xmlData["document_lines"][1]["line_quantity"]       = 1;
        $xmlData["document_lines"][1]["line_single_price"]   = $totalnetto;
        $xmlData["document_lines"][1]["line_total_price"]    = $totalnetto;
        $xmlData["document_lines"][1]["line_vat_percentage"] = 22;

        $xmlData["sales_totale_netto"] = $totalnetto;
        $xmlData["sales_totale_vat"]   = $totalnetto * 0.22;
        $xmlData["sales_totale"]       = $totalbrutto;

        $XMLInvoice->setData($xmlData);
        return $XMLInvoice;
    }
}

class OrderException extends \Exception
{
}