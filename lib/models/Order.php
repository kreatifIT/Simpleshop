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
        ]);
        if (!$raw) {
            $_products = $products;
            $products  = [];

            foreach ($_products as $product) {
                $_product = $product->getValue('data');
                $_data    = $product->getData();
                $_pdata   = $_product->getData();

                foreach ($_data as $key => $value) {
                    if (!array_key_exists($key, $_pdata)) {
                        $_product->setValue($key, $value);
                    }
                }
                $products[] = $_product;
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
            $products   = Session::getCartItems(false, false);

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
                OrderProduct::create()->setValue('order_id', $order_id)->setValue('product_id', $product->getValue('id'))->setValue('code', $product->getValue('code'))->setValue('quantity', $quantity)->setValue('data', $product)->setValue('createdate', $date_now)->save(true);
            }
        }
        return $result;
    }

    public function calculateDocument()
    {
        $errors               = [];
        $taxes                = [];
        $promotions           = [];
        $net_prices           = [];
        $this->quantity       = 0;
        $this->discount       = 0;
        $this->shipping_costs = 0;
        $this->initial_total  = 0;

        try {
            $products = Session::getCartItems();
        }
        catch (CartException $ex) {
            if ($ex->getCode() == 1) {
                $errors   = Session::$errors;
                $products = Session::getCartItems();
            }
        }
        // calculate total
        foreach ($products as $product) {
            $quantity = $product->getValue('cart_quantity');
            $tax_perc = Tax::get($product->getValue('tax'))->getValue('tax');

            $net_prices[$tax_perc] += (float) $product->getPrice() * $quantity;
            $this->initial_total += (float) $product->getPrice(true) * $quantity;
            $this->quantity += $quantity;
        }
        // get shipping costs
        try {
            if ($this->shipping) {
                $this->setValue('shipping_costs', (float) $this->shipping->getNetPrice($this, $products));
                $this->setValue('initial_shipping_costs', (float) $this->shipping->getPrice($this, $products));
            }
        }
        catch (\Exception $ex) {
            throw new OrderException($ex->getLabelByCode());
        }
        ksort($net_prices);

        $this->setValue('updatedate', date('Y-m-d H:i:s'));
        $this->setValue('ip_address', rex_server('REMOTE_ADDR', 'string', 'notset'));
        $this->setValue('net_prices', $net_prices);
        $this->setValue('subtotal', array_sum($net_prices));
        $this->setValue('total', array_sum($net_prices));

        try {
            $_promotions = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.applyDiscounts', [$this->getValue('discount')], ['Order' => $this]));
        }
        catch (\Exception $ex) {
            $_promotions = [];
            $errors[]    = ['label' => $ex->getLabelByCode()];
        }

        // set promotions for order history
        foreach ($_promotions as $name => $_promotion) {
            $promotion = null;
            try {
                if (is_object($_promotion)) {
                    $promotion = $_promotion->applyToOrder($this);
                }
                else if (is_numeric($_promotion)) {
                    foreach ($net_prices as &$net_price) {
                        if ($_promotion <= 0) {
                            break;
                        }
                        else if ($_promotion < $net_price) {
                            $net_price -= $_promotion;
                            $this->discount -= $_promotion;
                            $_promotion = 0;
                        }
                        else {
                            $_promotion -= $net_price;
                            $this->discount -= $net_price;
                            $net_price = 0;
                        }
                    }
                }
            }
            catch (\Exception $ex) {
                $errors[] = ['label' => $ex->getLabelByCode()];
            }
            if ($promotion) {
                $promotions[$name] = $promotion;
            }
        }

        $this->setValue('net_prices', $net_prices);
        $this->setValue('subtotal', array_sum($net_prices));

        // set tax values
        foreach ($this->net_prices as $tax => $net_price) {
            $taxes[$tax] += (float) $net_price / 100 * $tax;
        }
        if ($this->shipping_costs) {
            $tax = $this->shipping->getTaxPercentage();
            $taxes[$tax] += (float) $this->shipping_costs / 100 * $tax;
        }
        ksort($taxes);

        $this->setValue('promotions', $promotions);
        $this->setValue('taxes', $taxes);
        $this->setValue('total', $this->shipping_costs + array_sum($this->net_prices) + array_sum($taxes));

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
        return $errors;
    }

    public static function ext_yform_saved($params)
    {
        $result = $params->getSubject();

        if ($result !== false && $params->getParam('table') == self::TABLE) {
            $dataset = $params->getParam('form')->getParam('manager_dataset');
            if (!$dataset) {
                $dataset = rex_yform_manager_dataset::getRaw($params->getParam('id'), $table->getTableName());
            }
        }
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