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
        $Address = $this->getInvoiceAddress();
        $Country = Country::get($Address->getValue('country'));

        return $Country && !$Country->getValue('b2b_has_tax');
    }

    public function getProducts($raw = true)
    {
        $products = OrderProduct::getAll(false, [
            'filter'  => [['order_id', $this->getId()]],
            'orderBy' => 'id',
        ])->toArray();

        if (!$raw) {
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
        return $products;
    }

    public function getInvoiceNum()
    {
        return \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.getInvoiceNum', $this->getValue('invoice_num'), [
            'Order' => $this,
        ]));
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

        if (self::$_finalizeOrder && ($this->getValue('invoice_num') === null || $this->getValue('invoice_num') === 0)) {

            $sql->setQuery('SELECT MAX(invoice_num) as num FROM ' . Order::TABLE . ' WHERE createdate >= "' . date('Y-01-01 00:00:00') . '"');
            $value = $sql->getValue('num');
            $num   = (int) substr($value, 2) + 1;
            $num   = date('y') . str_pad($num, 5, '0', STR_PAD_LEFT);

            $this->setValue('invoice_num', \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.invoice_num', $num, ['Order' => $this, 'value' => $value])));
        }

        $result = parent::save(true);

        if ($result && !$simple_save) {

            $order_id   = $this->getValue('id');
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
                        }
                        else {
                            $product = Product::get($_product->getId());
                            $product->setValue('amount', $product->getValue('amount') + $quantity);
                            $product->save();
                        }
                    }
                }
            }
            // clear all products first
            \rex_sql::factory()->setQuery("DELETE FROM " . OrderProduct::TABLE . " WHERE order_id = {$order_id}");

            $products = Session::getCartItems(false, false);

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
                        }
                        else {
                            $product->setValue('amount', $product->getValue('amount') - $quantity);
                            $product->save();
                        }
                    }
                    if ($product->getValue('type') == 'giftcard') {
                        Coupon::createGiftcard($this, $product);
                    }
                }
                $OrderProduct = OrderProduct::create();
                $OrderProduct->setValue('order_id', $order_id);
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

    public function recalculateDocument($products = [], $promotions = [])
    {
        Utils::setCalcLocale();

        $Customer            = $this->getValue('customer_data');
        $net_prices          = [];
        $this->quantity      = 0;
        $this->discount      = 0;
        $this->initial_total = 0;
        $manual_discount     = $this->getValue('manual_discount', false, 0);

        // calculate total
        foreach ($products as $product) {
            $quantity = $product->getValue('cart_quantity');
            $tax_perc = Tax::get($product->getValue('tax'))->getValue('tax');

            $net_prices[$tax_perc] += (float) $product->getPrice() * $quantity;
            $this->initial_total   += (float) $product->getPrice(!$this->isTaxFree()) * $quantity;
            $this->quantity        += $quantity;
        }
        // get shipping costs
        if ($this->getValue('shipping')) {
            try {
                $this->setValue('shipping_costs', (float) $this->getValue('shipping')->getNetPrice($this, $products));
            }
            catch (\Exception $ex) {
                throw new OrderException($ex->getLabelByCode());
            }
        }
        else {
            $this->setValue('shipping_costs', 0);
        }
        ksort($net_prices);

        $this->setValue('updatedate', date('Y-m-d H:i:s'));
        $this->setValue('net_prices', $net_prices);
        $this->setValue('subtotal', array_sum($net_prices));
        $this->setValue('total', array_sum($net_prices));

        // reset manual discount
        unset($promotions['manual_discount']);

        // set promotions for order history
        list($errors, $_promotions) = $this->calculatePrices($promotions, $net_prices);

        // calculate manual discount
        if ($manual_discount > 0) {
            $netto_discount = 0;

            foreach ($this->net_prices as $tax_perc => $net_price) {
                if ($manual_discount <= 0) {
                    break;
                }

                $_ndiscount = $manual_discount / (100 + $tax_perc) * 100;

                if ($_ndiscount <= $net_price) {
                    $netto_discount  += $_ndiscount;
                    $manual_discount = 0;
                }
                else {
                    $_bdiscount      = $_ndiscount * (1 + $tax_perc / 100);
                    $manual_discount -= $_bdiscount;
                    $netto_discount  += $net_price;
                }
            }

            list($__errors, $__promotions) = $this->calculatePrices(['manual_discount' => $netto_discount], $this->net_prices);

            $errors      = array_merge($errors, $__errors);
            $_promotions = array_merge($_promotions, $__promotions);
        }

        $this->setValue('promotions', $_promotions);

        try {
            \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.calculateDocument', $this));
        }
        catch (\Exception $ex) {
            $errors[] = ['label' => $ex->getLabelByCode()];
        }

        // re-check order totals
        if ($this->total < 0) {
            $this->setValue('total', $this->total);
        }

        Utils::resetLocale();

        return $errors;
    }

    private function calculatePrices($promotions, $net_prices)
    {
        $taxes       = [];
        $errors      = [];
        $_promotions = [];
        $brut_prices = [];
        $Settings    = \rex::getConfig('simpleshop.Settings');

        foreach ($promotions as $name => $_promotion) {
            $promotion = null;
            try {
                if (is_object($_promotion)) {
                    $promotion = $_promotion->applyToOrder($this);
                }
                else if (is_numeric($_promotion) && $_promotion > 0) {
                    $__promotion = $_promotion;

                    foreach ($net_prices as &$net_price) {
                        if ($__promotion <= 0) {
                            break;
                        }
                        if ($__promotion < $net_price) {
                            $net_price      -= $__promotion;
                            $this->discount -= $__promotion;
                            $__promotion    = 0;
                            $promotion      = $_promotion;
                        }
                        else {
                            $__promotion    -= $net_price;
                            $this->discount -= $net_price;
                            $promotion      += $net_price;
                            $net_price      = 0;
                        }
                    }
                }
            }
            catch (\Exception $ex) {
                $errors[] = ['label' => $ex->getLabelByCode()];
            }
            if ($promotion) {
                $_promotions[$name] = $promotion;
            }
        }

        $this->setValue('net_prices', $net_prices);
        $this->setValue('subtotal', array_sum($net_prices));

        // set tax values
        foreach ($net_prices as $tax => $net_price) {
            $brut_prices[$tax] += (float) $net_price / (100 + $tax) * 100;
            $taxes[$tax]       += (float) $net_price / (100 + $tax) * $tax;
        }

        $this->setValue('brut_prices', $brut_prices);


        if ($this->getValue('shipping_costs') > 0 && !$this->isTaxFree()) {
            $tax = $this->getValue('shipping')->getTaxPercentage();

            if ($tax > 0) {
                $this->setValue('shipping_costs', (float) $this->getValue('shipping_costs') / (100 + $tax) * 100);
                $taxes[$tax] += $this->getValue('shipping')->getTax();
            }
        }
        ksort($taxes);

        $this->setValue('taxes', $taxes);
        $this->setValue('total', $this->getValue('shipping_costs') + array_sum($brut_prices) + array_sum($taxes));

        return [$errors, $_promotions];
    }

    public function calculateDocument(CheckoutController $caller) // $caller is used to prevent unauthorized method calls
    {
        try {
            $products = Session::getCartItems();
        }
        catch (CartException $ex) {
            if ($ex->getCode() == 1) {
                $errors   = Session::$errors;
                $products = Session::getCartItems();
            }
        }

        try {
            $promotions = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.applyDiscounts', [$this->getValue('discount')], ['Order' => $this]));
        }
        catch (\Exception $ex) {
            $promotions = [];
            $errors[]   = ['label' => $ex->getLabelByCode()];
        }

        $this->setValue('ip_address', rex_server('REMOTE_ADDR', 'string', 'notset'));

        return $this->recalculateDocument($products, $promotions);
    }

    public static function ext_yform_data_delete($params)
    {
        $result = $params->getSubject();

        if ($result !== false && $params->getParam('table')->getTableName() == self::TABLE) {
            // remove all related order products
            $obj_id = $params->getParam('data_id');
            $query  = "DELETE FROM " . OrderProduct::TABLE . " WHERE order_id = {$obj_id}";
            $sql    = \rex_sql::factory();
            $sql->setQuery($query);
        }
        return $result;
    }
}

class OrderException extends \Exception
{
}