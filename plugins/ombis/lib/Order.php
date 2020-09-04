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

use FriendsOfREDAXO\Simpleshop\Ombis\Customer\Address;
use FriendsOfREDAXO\Simpleshop\Session;
use FriendsOfREDAXO\Simpleshop\Settings;
use FriendsOfREDAXO\Simpleshop\Utils;
use Kreatif\WSConnectorException;


class Order
{

    public static function createPreVKDokument(\FriendsOfREDAXO\Simpleshop\Order $order)
    {
        $orderSync = \FriendsOfREDAXO\Simpleshop\Settings::getValue('ombis_order_sync', 'Ombis');

        if ($orderSync == 'address_sync') {
            $invoiceAddress  = $order->getInvoiceAddress();
            $shippingAddress = $order->getShippingAddress();

            try {
                if (!$invoiceAddress->valueIsset('ombis_uid') && $_address = Address::findOrCreateByAddress($invoiceAddress, 'invoice')) {
                    $order->setValue('invoice_address', $_address);
                }
                if ($_address = Address::findOrCreateByAddress($shippingAddress, 'shipping')) {
                    $order->setValue('shipping_address', $_address);
                }
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
                $docPositions['Delete'][] = $_position->URI;
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
                    'Price'              => number_format($promotion->getValue('value') * -1, 2, '.', ''),
                    'Quantity'           => '1',
                    'PricePerQuantity'   => '1',
                    'UnitCode'           => 'ST',
                    'PreiseUebernehmen'  => '1',
                    'RabatteUebernehmen' => '1',
                ],
            ];
        }

        $data = \rex_extension::registerPoint(new \rex_extension_point('Ombis.orderData', [
            'Fields'      => [
                'CustomerNo'              => $dummyId,
                'TypeOfPaymentCode'       => $paymentConfig[$payment->getPluginName()],
                'CustomerReferenceNumber' => (string)$order->getValue('id'),
                'CustomerReferenceDate'   => date('Y-m-d'),
                'InvoiceAddressUUID'      => $invoiceAddress->getValue('ombis_uid'),
                'ShippingAddressUUID'     => $shippingAddress->getValue('ombis_uid'),
                'Notes'                   => (string)$order->getValue('remarks'),
                'DocType'                 => 'VkAuftrag',
            ],
            'Collections' => [
                'DocPosition' => $docPositions,
            ],
        ]));

        //pr($data, 'blue');
        //pr(json_encode($data));
        //exit;

        $path     = $ombisId == '' || $ombisId == 0 ? '' : "/{$ombisId}";
        $method   = $ombisId == '' || $ombisId == 0 ? 'POST' : 'PUT';
        $response = Api::curl('/preliminarysalesdocument' . $path, $data, $method);

        if (isset($response['last_id'])) {
            $order->setValue('ombis_id', $response['last_id']);
            $order->save();
        }
        return $order;
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