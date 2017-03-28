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

    private $finalizeOrder = false;

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

    public function completeOrder()
    {
        $this->finalizeOrder = true;
        return $this->save(false);
    }

    public function save($simple_save = true)
    {
        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.preSave', $this, ['create_order' => $create_order, 'simple_save' => $simple_save]));

        $date_now = date('Y-m-d H:i:s');
        $this->setValue('createdate', $date_now);

        $result = parent::save(true);

        if ($result && !$simple_save) {

            $order_id   = $this->getValue('id');
            $promotions = $this->getValue('promotions');
            $products   = Session::getCartItems(false, false);

            if ($this->finalizeOrder && isset ($promotions['coupon'])) {
                // relate coupon
                $promotions['coupon']->linkToOrder($this);
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
                if ($this->finalizeOrder) {
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
        $errors         = [];
        $this->tax      = 0;
        $this->subtotal = 0;
        $this->quantity = 0;
        $this->discount = 0;

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
            $this->subtotal += (float) $product->getPrice(true) * $quantity;
            $this->tax += (float) $product->getTax() * $quantity;
            $this->quantity += $quantity;
        }
        // get shipping costs
        try {
            $this->shipping_costs = (float) $this->shipping ? $this->shipping->getPrice($this, $products) : 0;
        }
        catch (\Exception $ex) {
            throw new OrderException($ex->getLabelByCode());
        }
        $this->tax += (float) $this->shipping ? $this->shipping->getTax() : 0;

        $this->updatedate    = date('Y-m-d H:i:s');
        $this->ip_address    = rex_server('REMOTE_ADDR', 'string', 'notset');
        $this->initial_total = $this->subtotal + $this->shipping_costs;
        $this->total         = $this->initial_total - $this->discount;
        $this->promotions    = []; // clear promotions!


        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Order.calculateDocument', $this));

        // add promotions
        $promotions = [];

        // set promotions for order history
        $_promotions = $this->getValue('promotions');

        if ($_promotions) {
            foreach ($_promotions as $name => $_promotion) {
                $promotion = null;
                try {
                    $promotion = $_promotion->applyToOrder($this);
                }
                catch (\Exception $ex) {
                    $errors[] = ['label' => $ex->getLabelByCode()];
                }
                if ($promotion) {
                    $promotions[$name] = $promotion;
                }
            }
        }
        $this->setValue('promotions', $promotions);

        // re-check order totals
        if ($this->total < 0) {
            $this->total = 0;
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