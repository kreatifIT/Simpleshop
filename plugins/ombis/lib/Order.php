<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 16.06.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop\Ombis;

use FriendsOfREDAXO\Simpleshop\CustomerAddress;
use FriendsOfREDAXO\Simpleshop\Model;
use FriendsOfREDAXO\Simpleshop\Ombis\Customer\Customer;
use FriendsOfREDAXO\Simpleshop\Session;
use FriendsOfREDAXO\Simpleshop\Settings;
use FriendsOfREDAXO\Simpleshop\Utils;
use Kreatif\WSConnectorException;


class Order
{

    public static function createPreVKDokument(\FriendsOfREDAXO\Simpleshop\Order $order)
    {
        $orderSync = \FriendsOfREDAXO\Simpleshop\Settings::getValue('ombis_order_sync', 'Ombis');

        if ($orderSync == 'customer_sync') {
            $customer        = $order->getCustomerData();
            $invoiceAddress  = $order->getInvoiceAddress();
            $shippingAddress = $order->getShippingAddress();

            $customer->setValue('ombis_id', \FriendsOfREDAXO\Simpleshop\Customer::get($customer->getId())->getValue('ombis_id'));

            if ($invoiceAddress) {
                $invoiceAddress->setValue('ombis_id', CustomerAddress::get($invoiceAddress->getId())->getValue('ombis_id'));
                $invoiceAddress->setValue('ombis_uid', CustomerAddress::get($invoiceAddress->getId())->getValue('ombis_uid'));
            }
            if ($shippingAddress) {
                $shippingAddress->setValue('ombis_id', CustomerAddress::get($shippingAddress->getId())->getValue('ombis_id'));
                $shippingAddress->setValue('ombis_uid', CustomerAddress::get($shippingAddress->getId())->getValue('ombis_uid'));
            }

            try {
                $sql = \rex_sql::factory();
                [$customer, $invoiceAddress, $shippingAddress] = Customer::write($customer, $invoiceAddress, $shippingAddress);

                $sql->setTable(\FriendsOfREDAXO\Simpleshop\Customer::TABLE);
                $sql->setValue('ombis_id', $customer->getValue('ombis_id'));
                $sql->setWhere('id = :id', ['id' => $customer->getId()]);
                $sql->update();

                $order->setValue('customer_data', $customer);
                $order->setValue('invoice_address', $invoiceAddress);
                $order->setValue('shipping_address', $shippingAddress);
                $_order = Model::prepare($order);

                $sql = \rex_sql::factory();
                $sql->setTable(\FriendsOfREDAXO\Simpleshop\Order::TABLE);
                $sql->setValue('customer_data', $_order['customer_data']);
                $sql->setValue('invoice_address', $_order['invoice_address']);
                $sql->setValue('shipping_address', $_order['shipping_address']);
                $sql->setWhere('id = :id', ['id' => $order->getId()]);
                $sql->update();

                $order->invalidateData();
                $order = self::write($order);
            } catch (WSConnectorException $ex) {
                // just go ahead
                Utils::log('Ombis.WSConnectorException', $ex->getMessage(), 'ERROR', true);
            }
            Session::setCheckoutData('Order', $order);
        } else {
            // do other sync??
        }
        return $order;
    }

    public static function write(\FriendsOfREDAXO\Simpleshop\Order $order)
    {
        $docPositions    = ['Data' => []];
        $dummyId         = Settings::getValue('order_dummy_id', 'Ombis');
        $paymentConfig   = Settings::getValue('ombis_payment_config', 'Ombis');
        $shippingCode    = Settings::getValue('order_shipping_code', 'Ombis');
        $discountCode    = Settings::getValue('order_disount_code', 'Ombis');
        $paymentTerm     = Settings::getValue('ombis_payment_term', 'Ombis');
        $customer        = $order->getCustomerData();
        $invoiceAddress  = $order->getInvoiceAddress();
        $shippingAddress = $order->getShippingAddress();
        $payment         = $order->getValue('payment');
        $shippingCosts   = (float)$order->getValue('shipping_costs');
        $ombisId         = trim($order->getValue('ombis_id'));
        $orderProducts   = $order->getProducts();

        if ($ombisId != '' && $ombisId != 0) {
            // remove existing document positions
            $_positions = Api::curl("/preliminarysalesdocument/{$ombisId}/docposition");

            foreach ($_positions['Data'] as $_position) {
                $docPositions['Delete'][] = (string)$_position['URI'];
            }
        }

        foreach ($orderProducts as $orderProduct) {
            $product = $orderProduct->getValue('data');
            $vintage = $product->getFeatureValue('vintage');

            $docPositions['Data'][] = [
                'Fields' => [
                    'ItemCode'                 => $product->getValue('code'),
                    'Quantity'                 => $orderProduct->getValue('cart_quantity'),
                    'Price'                    => number_format($product->getPrice(false), 2, '.', ''),
                    'PricePerQuantity'         => '1',
                    'PreiseUebernehmen'        => '1',
                    'RabatteUebernehmen'       => '1',
                    'MemProperties.MerkmalALK' => number_format($product->getValue('alcohol'), 2, '.', ''),
                    'MemProperties.MerkmalJG'  => $vintage ? $vintage->getValue('key') : '',
                ],
            ];
        }

        if ($shippingCode && $shippingCosts) {
            // shippingcosts
            $docPositions['Data'][] = [
                'Fields' => [
                    'ItemCode'           => $shippingCode,
                    'Price'              => number_format($shippingCosts / 122 * 100, 2, '.', ''),
                    'Quantity'           => '1',
                    'PricePerQuantity'   => '1',
                    'UnitCode'           => 'ST',
                    'PreiseUebernehmen'  => '1',
                    'RabatteUebernehmen' => '1',
                ],
            ];
        }

        // promotions
        foreach ($order->getValue('promotions') as $promotion) {
            $docPositions['Data'][] = [
                'Fields' => [
                    'ItemCode'           => $discountCode,
                    'Price'              => number_format($promotion->getValue('value') / -1.22, 2, '.', ''),
                    'Quantity'           => '1',
                    'PricePerQuantity'   => '1',
                    'UnitCode'           => 'ST',
                    'PreiseUebernehmen'  => '1',
                    'RabatteUebernehmen' => '1',
                ],
            ];
        }

        $customerId = $customer ? $customer->getValue('ombis_id') : '';
        $customerId = $customerId == '' ? $dummyId : $customerId;

        $data = \rex_extension::registerPoint(new \rex_extension_point('Ombis.orderData', [
            'Fields'      => [
                'Customer'                => (string)$customerId,
                'TypeOfPayment'           => (string)$paymentConfig[$payment->getPluginName()],
                'TermOfPayment'           => (string)$paymentTerm,
                'CustomerReferenceNumber' => (string)$order->getValue('id'),
                'CustomerReferenceDate'   => date('Y-m-d'),
                'InvoiceAddressUUID'      => $invoiceAddress->getValue('ombis_uid'),
                'Notes'                   => (string)$order->getValue('remarks'),
                'DocType'                 => 'VkAuftrag',
            ],
            'Collections' => [
                'DocPosition' => $docPositions,
            ],
        ], [
            'order'          => $order,
            'customer'       => $customer,
            'invoiceAddress' => $invoiceAddress,
            'shippingAddress' => $shippingAddress,
        ]));
        if ($shippingAddress && $invoiceAddress->getId() != $shippingAddress->getId()) {
            $data['Fields']['ShippingAddressUUID'] = $shippingAddress->getValue('ombis_uid');
        }

        $path     = $ombisId == '' || $ombisId == 0 ? '' : "/{$ombisId}";
        $method   = $ombisId == '' || $ombisId == 0 ? 'POST' : 'PUT';
        $response = Api::curl('/preliminarysalesdocument' . $path, $data, $method);

        if (isset($response['last_id'])) {
            $order->setValue('ombis_id', $response['last_id']);
            $order->save();
        }
        return $order;
    }

    public static function ext__orderFunctionsOutput(\rex_extension_point $ep)
    {
        $output = $ep->getSubject();
        $order  = $ep->getParam('order');
        $action = $ep->getParam('action');

        if ($action == 'write-to-ombis') {
            try {
                $_ombisId = $order->getValue('ombis_id');

                if ($_ombisId != '') {
                    Api::curl("/preliminarysalesdocument/{$_ombisId}", [], 'GET', [], false, false);
                }

                $order   = self::createPreVKDokument($order);
                $ombisId = $order->getValue('ombis_id');

                if ($ombisId == 0 || $ombisId == '') {
                    echo \rex_view::error('PreVKDokument konnte nicht übermittelt werden');
                } else if ($_ombisId == '' || $_ombisId == 0) {
                    echo \rex_view::info('Neues PreVKDokument erstellt mit ID = ' . $ombisId);
                } else {
                    echo \rex_view::info('PreVKDokument mit ID = ' . $ombisId . ' wurde aktualisiert');
                }
            } catch (\Exception $ex) {
                if ($ex->getCode() == 3) {
                    echo \rex_view::warning("PreVKDokument mit ID {$_ombisId} existiert nicht mehr");
                } else {
                    echo \rex_view::error($ex->getMessage());
                }
            }
        }

        $output[] = '
             <a href="' . \rex_url::currentBackendPage([
                'table_name' => \FriendsOfREDAXO\Simpleshop\Order::TABLE,
                'data_id'    => $order->getId(),
                'func'       => rex_request('func', 'string'),
                'ss-action'  => 'write-to-ombis',
                'ts'         => time(),
            ]) . '" class="btn btn-default">
                    <i class="fa fa-database"></i>&nbsp;
                    an Ombis übermitteln
            </a>
        ';
        $ep->setSubject($output);
    }

    public static function ext__createPreVKDokument(\rex_extension_point $ep)
    {
        $saveSuccess = $ep->getSubject();

        if ($saveSuccess) {
            $order = $ep->getParam('Order');
            self::createPreVKDokument($order);
        }
        $ep->setSubject($saveSuccess);
    }
}