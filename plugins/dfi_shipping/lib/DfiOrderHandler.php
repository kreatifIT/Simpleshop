<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

class DfiOrderHandler
{
    public static function ext_completeOrder(\rex_extension_point $ep): void
    {
        $order = $ep->getParam('Order');

        if (!$order instanceof Order || !$order->getValue('shipping') instanceof DfiShipping) {
            return;
        }

        self::submit($order);
    }

    /**
     * Transmits the completed order to DFI via /v2/addorder.
     * Always records the transmission timestamp and the raw response (or
     * error) on the order, and never lets a failed transmission interrupt
     * order completion - failures are logged instead.
     */
    public static function submit(Order $order): void
    {
        $address = $order->getShippingAddress();

        if (!$address) {
            return;
        }

        $Country      = $address->getCountry();
        $customer     = $order->getCustomerData();
        $customerData = [
            'customer' => $address->getName(),
            'address'  => trim($address->getValue('street')),
            'city'     => $address->getValue('location'),
            'state'    => '',
            'postcode' => $address->getValue('postal'),
            'country'  => $Country ? $Country->getValue('iso2') : '',
            'phone'    => $address->getValue('phone'),
            'email'    => $customer ? $customer->getValue('email') : '',
        ];

        $products = [];
        foreach ($order->getOrderProducts() as $product) {
            $hasVariant = (string) $product->getValue('variant_key') !== '';
            $qty        = (int) $product->getValue('cart_quantity');

            $products[] = [
                'qty'               => $qty,
                'sku'               => (string) ($hasVariant ? $product->getValue('code') : $product->getValue('ax_code')),
                'total_without_tax' => (float) $product->getPrice(false) * $qty,
            ];
        }

        // shipping_api_response holds the raw /v2/shipping response recorded
        // during checkout - read shipping_cost/fees from there rather than
        // via the (by-then freshly reconstructed) shipping plugin instance.
        $rawShippingResponse = json_decode((string) $order->getValue('shipping_api_response'), true) ?: [];
        $shippingCost        = (float) ($rawShippingResponse['shipping_cost'] ?? $order->getValue('shipping_costs'));
        $fees                = (float) ($rawShippingResponse['fees'] ?? 0);

        $order->setValue('dfi_addorder_sent_at', date('Y-m-d H:i:s'));

        try {
            $dfi      = new Dfi();
            $response = $dfi->addOrder(
                $order->getReferenceId(),
                $customerData,
                $products,
                [
                    'country'       => $customerData['country'],
                    'insurance'     => false,
                    'order_notes'   => trim((string) $order->getValue('remarks')),
                    'total'         => (float) $order->getTotal(),
                    'vat'           => array_sum((array) $order->getValue('taxes')),
                    'shipping_cost' => $shippingCost,
                    'fees'          => $fees,
                ]
            );
            $order->setValue('dfi_addorder_response', json_encode($response));
        } catch (DfiException $e) {
            \rex_logger::logException($e);
            $order->setValue('dfi_addorder_response', json_encode(['error' => $e->getMessage()]));
        }

        $order->save();
    }
}
