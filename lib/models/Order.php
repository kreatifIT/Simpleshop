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

use Kreatif\Model;


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

    public static function findByPaymentToken()
    {
        $order    = null;
        $payments = Payment::getAll();

        foreach ($payments as $payment) {
            $order = $payment->getOrderByPaymentToken();

            if ($order) {
                break;
            }
        }
        if (!$order) {
            $order = Session::getCurrentOrder();
        }
        return $order;
    }

    public function getShippingAddress()
    {
        return $this->getValue('shipping_address') == '' ? $this->getValue('invoice_address') : $this->getValue('shipping_address');
    }

    public function getInvoiceAddress()
    {
        return $this->getValue('invoice_address');
    }

    public function getCustomerData()
    {
        $customer     = $this->getValue('customer_data');
        $customerData = $customer ? $customer->getData() : [];
        $customerId   = $this->getValue('customer_id');


        if (!$customer || ($customerId && $customerData['id'] != $customerId)) {
            $customer = $customerId ? Customer::get($customerId) : null;
            $customer = $customer ?: self::getInvoiceAddress();
            $this->setValue('customer_data', $customer);

            if ($this->getId() && $customer) {
                $sql = \rex_sql::factory();
                $sql->setTable(self::TABLE);
                $sql->setValue('customer_data', $this->getRawValue('customer_data'));
                $sql->setWhere(['id' => $this->getId()]);
                $sql->update();
            }
        }
        return $customer;
    }

    public function isTaxFree()
    {
        $Address = $this->getInvoiceAddress();
        return $Address ? $Address->isTaxFree() : false;
    }

    public function getSubtotal($includeTax = true)
    {
        if ($includeTax) {
            $subtotal = $this->getGrossTotal();
        } else {
            $subtotal = $this->getNettoTotal();
        }
        return $subtotal;
    }

    public function getGrossTotal()
    {
        return array_sum((array)$this->getValue('brut_prices'));
    }

    public function getNettoTotal($applyShippingAndDiscounts = false)
    {
        $total = array_sum((array)$this->getValue('net_prices'));

        if ($applyShippingAndDiscounts) {
            if ($this->getValue('shipping_costs') > 0) {
                $shipping = $this->getValue('shipping');

                if ($shipping) {
                    $total += $shipping->getNetPrice($this);
                }
            }
            $total -= $this->getDiscount(false);
        }
        return $total;
    }

    public function getTotal()
    {
        return $this->getValue('total');
    }

    public function getDiscount($includeTax = true)
    {
        $discount = $this->getValue('discount');

        if (!$includeTax) {
            $grossTotal = array_sum($this->getValue('brut_prices'));
            $nettoTotal = array_sum($this->getValue('net_prices'));
            $_percent   = $discount / $grossTotal;
            $discount   = $nettoTotal * $_percent;
        }
        return $discount;
    }

    public function getShippingCosts()
    {
        return $this->getValue('shipping_costs');
    }

    public function getOrderProducts()
    {
        $products = (array)$this->getValue('products');

        if (!$products) {
            $stmt = OrderProduct::query();
            $stmt->where('order_id', $this->getId());
            $stmt->orderBy('id');
            $_products = $stmt->find();

            foreach ($_products as $orderProduct) {
                $products[] = $orderProduct->getProduct();
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
        $image = '';
        $key   = $this->getShippingKey(true);

        if (strlen($key) == 12 || strlen($key) == 13) {
            $Barcode = new \barcode_generator();
            $image   = $Barcode->render_image('code-128', $key, [
                'ph' => 0,
                'sf' => 1,
                'sx' => 2,
                'pt' => 0,
                'pb' => 16,
                'ts' => 12,
                'th' => 16,
                'wq' => 0,
            ]);
            ob_start();
            imagepng($image);
            $image = 'data:image/png;base64,' . base64_encode(ob_get_clean());
        }
        return $image;
    }

    public function completeOrder()
    {
        self::$_finalizeOrder = true;

        $result = $this->save(false);

        return \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.completeOrder', $result, [
            'Order' => $this,
        ]));
    }

    public function save($simple_save = true, $products = null)
    {
        Utils::setCalcLocale();

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.preSave', $this, ['finalize_order' => self::$_finalizeOrder, 'simple_save' => $simple_save]));

        $sql            = \rex_sql::factory();
        $date_now       = date('Y-m-d H:i:s');
        $already_exists = !($this->getValue('invoice_num') === null || (int)$this->getValue('invoice_num') === 0);

        // set customer data
        $this->getCustomerData();


        if (!$this->valueIsset('createdate')) {
            $this->setValue('createdate', $date_now);
        }
        if (!$this->valueIsset('shipping_address_id')) {
            $shippingAddress = $this->getShippingAddress();
            if ($shippingAddress) {
                $this->setValue('shipping_address_id', $shippingAddress->getId());
            }
        }

        if (self::$_finalizeOrder && !$already_exists) {
            $query = 'SELECT IFNULL(MAX(invoice_num), 0) + 1 as num FROM ' . Order::TABLE . ' WHERE createdate >= "' . date('Y-01-01 00:00:00') . '"';
            $sql->setQuery($query);
            $num = $sql->getValue('num');
            $num = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.invoice_num', $num, ['Order' => $this, 'value' => $value]));

            if ($num == 0) {
                $num = null;
            }
            $this->setValue('invoice_num', $num);
        }

        $products    = $products ?: $this->getValue('products');
        $saveSuccess = parent::save();
        $this->setValue('products', $products);

        if (!$saveSuccess) {
            throw new OrderException(implode('<br/>', $this->getMessages()));
        }


        if ($saveSuccess && !$simple_save) {
            $promotions = $this->getValue('promotions');

            // IMPORTANT! after saving read the products
            if ($products === null) {
                $products = (array)$this->getOrderProducts();
            }
            $this->setValue('products', $products);

            if (self::$_finalizeOrder && isset ($promotions['coupon'])) {
                // relate coupon
                $promotions['coupon']->linkToOrder($this);
            }

            // reset quanities
            if ($already_exists) {
                $query = OrderProduct::query();
                $query->where('order_id', $this->getId());
                $query->orderBy('id');
                $orderProducts = $query->find();

                foreach ($orderProducts as $order_product) {
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
            }

            // set order products
            $orderProductIds = [];
            foreach ($products as $product) {
                $orderProductIds[] = $this->saveOrderProduct($product, self::$_finalizeOrder);
            }

            // clear deleted products
            $where = ["order_id = {$this->getId()}"];
            if (count($orderProductIds)) {
                $where[] = 'id NOT IN(' . implode(',', $orderProductIds) . ')';
            }
            $sql->setTable(OrderProduct::TABLE);
            $sql->setWhere(implode(' AND ', $where));
            $sql->delete();
        }
        Utils::resetLocale();
        return $saveSuccess;
    }

    public function saveOrderProduct($product, $trackInventory = false, $OrderProduct = null)
    {
        $quantity = $product->getValue('cart_quantity');

        if ($trackInventory) {
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

        if (!$OrderProduct) {
            $OrderProduct = OrderProduct::getByProductKey($this->getId(), $product->getKey()) ?: OrderProduct::create();
        }
        $OrderProduct->setValue('data', $product);
        $OrderProduct->setValue('product_id', $product->getValue('id'));
        $OrderProduct->setValue('variant_key', $product->getValue('variant_key'));
        $OrderProduct->setValue('code', $product->getValue('code'));
        $OrderProduct->setValue('cart_quantity', $quantity);
        $OrderProduct->setValue('order_id', $this->getId());

        $OrderProduct = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.saveOrderProduct', $OrderProduct, [
            'Order'   => $this,
            'product' => $product,
        ]));

        $OrderProduct->save();
        return $OrderProduct->getId();
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
                $tax_perc = $this->isTaxFree() ? 0 : Tax::get($product->getValue('tax'))
                    ->getValue('tax');

                $net_prices[$tax_perc]   += (float)$product->getPrice(false) * $quantity;
                $gross_prices[$tax_perc] += (float)$product->getPrice(!$this->isTaxFree()) * $quantity;
                $this->quantity          += $quantity;
            }
        }
        ksort($gross_prices);
        ksort($net_prices);

        $this->setValue('updatedate', date('Y-m-d H:i:s'));
        $this->setValue('net_prices', $net_prices);
        $this->setValue('brut_prices', $gross_prices);
        $this->setValue('initial_total', array_sum($gross_prices));

        $shipping = $this->getValue('shipping');

        // get shipping costs
        if ($shipping && $shipping->hasCosts()) {
            try {
                $this->setValue('shipping_costs', (float)$shipping->getGrossPrice($this, $products));
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
        $total        = $ReferenceOrder->getValue('total') * -1;
        $gross_prices = $ReferenceOrder->getValue('brut_prices');

        foreach ($gross_prices as &$gross_price) {
            $gross_price = $gross_price * -1;
        }

        $this->setValue('customer_id', $ReferenceOrder->getValue('customer_id'));
        $this->setValue('status', 'CN');
        $this->setValue('initial_total', $total);
        $this->setValue('brut_prices', $gross_prices);
        $this->setValue('address_1', $ReferenceOrder->getValue('address_1'));
        $this->setValue('ip_address', rex_server('REMOTE_ADDR', 'string', 'notset'));
        $this->setValue('total', $total);
        $this->setValue('ref_order_id', $ReferenceOrder->getId());

        self::$_finalizeOrder = true;

        return $gross_prices;
    }

    private function calculatePrices($promotions)
    {
        $discount        = 0;
        $taxes           = [];
        $errors          = [];
        $_promotions     = [];
        $hasFreeShipping = false;
        $gross_prices    = $this->getValue('brut_prices');


        foreach ($promotions as $promotion) {
            if ($promotion->getValue('action') == 'free_shipping') {
                $hasFreeShipping = true;
                break;
            }
        }

        // set shipping costs
        if (!$hasFreeShipping && $this->getValue('shipping_costs') > 0) {
            $shipping   = $this->getValue('shipping');
            $taxPercent = $shipping->getTaxPercentage();

            $gross_prices[$taxPercent] += $shipping->getPrice($this);
        }

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
            $taxes[$tax] += (float)($gross_price / ($tax + 100) * $tax);
        }

        ksort($taxes);

        $this->setValue('taxes', $taxes);
        $this->setValue('discount', $discount);
        $this->setValue('total', array_sum($gross_prices));

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

        $this->recalculateDocument($products);

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
        $this->setValue('products', $products);
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

    public function getInvoicePDF($type = 'invoice', $debug = false, \Kreatif\Mpdf\Mpdf $_Mpdf = null)
    {
        if (!class_exists('Kreatif\Mpdf\Mpdf')) {
            return false;
        }

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

        FragmentConfig::$data['checkout']['show_tax_info'] = !$this->isTaxFree();

        // HEADER
        $content = $fragment->parse('simpleshop/pdf/invoice/header.php');
        // INVOICE DATA
        $content .= $fragment->parse('simpleshop/pdf/invoice/invoice_data.php');
        // ITEMS
        $content .= $fragment->parse('simpleshop/pdf/invoice/items.php');
        // SUMMARY
        $content .= $fragment->parse('simpleshop/pdf/invoice/summary.php');

        $docTitle = $type == 'invoice' ? 'simpleshop.invoice_title' : 'simpleshop.orderdocument_title';
        $fragment->setVar('title', strtr(\Wildcard::get($docTitle), ['{NUM}' => $invoiceNum]));
        $fragment->setVar('css_filename', 'shop.css');
        $fragment->setVar('content', $content, false);
        $html = $fragment->parse('simpleshop/pdf/invoice/wrapper.php');

        list($Mpdf, $html) = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.invoicePDFContent', [$Mpdf, $html], [
            'order' => $this,
        ]));
        if ($debug) {
            echo \Wildcard::parse("<div style='background:#666;height:100vh;padding:20px;'><div style='max-width:800px;margin:0px auto;background:#fff;'>{$html}</div></div>");
            exit;
        }
        $Mpdf->WriteHTML($html);

        return $Mpdf;
    }

    public function getPackingListPDF($debug = false)
    {
        $Customer = $this->getCustomerData();

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

        $fragment->setVar('title', strtr(\Wildcard::parse(\Wildcard::get('label.packing_list_title')), ['{NUM}' => $refId]));
        $fragment->setVar('css_filename', 'shop.css');
        $fragment->setVar('content', $content, false);
        $html = $fragment->parse('simpleshop/pdf/invoice/wrapper.php');

        if ($debug) {
            echo "<div style='background:#666;height:100vh;padding:20px;'><div style='max-width:800px;margin:0px auto;background:#fff;'>{$html}</div></div>";
            exit;
        }
        $Mpdf->WriteHTML($html);

        return $Mpdf;
    }

    public static function be__addProduct()
    {
        $product_id = \rex_api_simpleshop_be_api::$inst->request['productId'];
        $order_id   = \rex_api_simpleshop_be_api::$inst->request['orderId'];
        $Product    = $product_id ? Product::get($product_id) : null;
        $Order      = $order_id ? self::get($order_id) : null;

        if (!$Product) {
            \rex_api_simpleshop_be_api::$inst->errors[] = 'Product not found with ID: ' . $product_id;
        } else if (!$Order) {
            \rex_api_simpleshop_be_api::$inst->errors[] = 'Oder not found with ID: ' . $order_id;
        } else {
            $Product->setValue('cart_quantity', 1);
            $Order->saveOrderProduct($Product, true);

            $fragment = new \rex_fragment();
            $fragment->setVar('Order', $Order);
            \rex_api_simpleshop_be_api::$inst->response['html'] = $fragment->parse('simpleshop/backend/order_products.php');
        }
    }

    public static function be__removeProduct()
    {
        $product_id   = \rex_api_simpleshop_be_api::$inst->request['productId'];
        $order_id     = \rex_api_simpleshop_be_api::$inst->request['orderId'];
        $oldAmount    = \rex_api_simpleshop_be_api::$inst->request['old_amount'];
        $OrderProduct = $product_id ? OrderProduct::get($product_id) : null;
        $Order        = $order_id ? self::get($order_id) : null;

        if (!$OrderProduct) {
            \rex_api_simpleshop_be_api::$inst->errors[] = 'OderProduct not found with ID: ' . $product_id;
        } else if (!$Order) {
            \rex_api_simpleshop_be_api::$inst->errors[] = 'Oder not found with ID: ' . $order_id;
        } else {
            $Product = $OrderProduct->getValue('data');

            // update inventory
            if ($Product->getValue('inventory') == 'F') {
                if ($Product->getValue('variant_key')) {
                    $Variant = Variant::getByVariantKey($Product->getValue('key'));
                    $Variant->setValue('amount', $Variant->getValue('amount') + $oldAmount);
                    $Variant->save();
                } else {
                    $Product->setValue('amount', $Product->getValue('amount') + $oldAmount);
                    $Product->save();
                }
            }
            // remove the order product
            $OrderProduct->delete();

            $fragment = new \rex_fragment();
            $fragment->setVar('Order', $Order);
            \rex_api_simpleshop_be_api::$inst->response['html'] = $fragment->parse('simpleshop/backend/order_products.php');
        }
    }

    public static function be__changeProductQuantity()
    {
        $product_id   = \rex_api_simpleshop_be_api::$inst->request['productId'];
        $order_id     = \rex_api_simpleshop_be_api::$inst->request['orderId'];
        $amount       = \rex_api_simpleshop_be_api::$inst->request['quantity'];
        $oldAmount    = \rex_api_simpleshop_be_api::$inst->request['old_amount'];
        $OrderProduct = $product_id ? OrderProduct::get($product_id) : null;
        $Order        = $order_id ? self::get($order_id) : null;

        if (!$OrderProduct) {
            \rex_api_simpleshop_be_api::$inst->errors[] = 'OderProduct not found with ID: ' . $product_id;
        } else if (!$Order) {
            \rex_api_simpleshop_be_api::$inst->errors[] = 'Oder not found with ID: ' . $order_id;
        } else {
            $Product = $OrderProduct->getValue('data');

            // update inventory
            if ($Product->getValue('inventory') == 'F') {
                if ($Product->getValue('variant_key')) {
                    $Variant = Variant::getByVariantKey($Product->getValue('key'));
                    $Variant->setValue('amount', $Variant->getValue('amount') + $oldAmount);
                    $Variant->save();
                } else {
                    $Product->setValue('amount', $Product->getValue('amount') + $oldAmount);
                    $Product->save();
                }
            }

            $Product->setValue('cart_quantity', $amount);

            \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.before_be__changeProductQuantity', $Product, [
                'Order'        => $Order,
                'OrderProduct' => $OrderProduct,
            ]));

            $Order->saveOrderProduct($Product, true, $OrderProduct);

            $fragment = new \rex_fragment();
            $fragment->setVar('Order', $Order);
            \rex_api_simpleshop_be_api::$inst->response['html'] = $fragment->parse('simpleshop/backend/order_products.php');
        }
    }

    public function getXML()
    {
        $invoice_num = $this->getInvoiceNum();

        if (!$invoice_num) {
            return null;
        }

        $XMLInvoice  = XMLInvoice::factory();
        $address     = $this->getInvoiceAddress();
        $xmlData     = $XMLInvoice->getData();
        $iDateTs     = strtotime($this->getValue('createdate'));
        $iDate       = date('Y-m-d', $iDateTs);
        $totalnetto  = number_format(abs($this->getValue('total')) / 122 * 100, 2, '.', '');
        $totalbrutto = number_format($totalnetto * 1.22, 2, '.', '');

        $xmlData["document_lines"]                 = [];
        $xmlData['document_type']                  = $this->getValue('status') == 'CN' ? 'TD04' : 'TD01';
        $xmlData['receiver_name']                  = trim($address->getName());
        $xmlData['receiver_private_vat_number']    = mb_strtoupper($address->getValue('fiscal_code'));
        $xmlData['receiver_head_quarter_street']   = $address->getValue('street');
        $xmlData['receiver_head_quarter_zip']      = $address->getValue('postal');
        $xmlData['receiver_head_quarter_city']     = $address->getValue('location');
        $xmlData['receiver_head_quarter_province'] = 'BZ';
        $xmlData['receiver_head_quarter_nation']   = 'IT';

        $xmlData['document_date']   = $iDate;
        $xmlData['document_number'] = $invoice_num;

        $xmlData["document_lines"][1]["line_number"]         = 1;
        $xmlData["document_lines"][1]["line_description"]    = FragmentConfig::getValue('xml_general_line_description');
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