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
    /**
     * Fallback VAT rate when the invoice address' country has none configured
     * (rex_prj_country.vat_rate) - matches the rate all prices in the shop
     * are assumed to have been entered gross with.
     */
    const FALLBACK_VAT_RATE = 22.0;

    public static function ext_completeOrder(\rex_extension_point $ep): void
    {
        $order = $ep->getParam('Order');

        if (!$order instanceof Order || !$order->getValue('shipping') instanceof DfiShipping) {
            return;
        }

        self::submit($order);
    }

    /**
     * Registers the order as a pre-ordine with DFI (/v2/pad/addorder) right
     * at checkout completion - unlike submit(), this always runs
     * automatically and isn't gated behind a manual backend trigger.
     */
    public static function ext_submitPreOrder(\rex_extension_point $ep): void
    {
        $order = $ep->getParam('Order');

        if (!$order instanceof Order || !$order->getValue('shipping') instanceof DfiShipping) {
            return;
        }

        self::submitPreOrder($order);
    }

    /**
     * Transmits the completed order to DFI via /v2/addorder.
     * dfi_addorder_sent_at is only set on a successful transmission, so it
     * reflects whether the order actually reached DFI, not just that a
     * submission was attempted; the raw response (or error) is always
     * recorded regardless, and a failed transmission never interrupts order
     * completion - failures are logged instead.
     */
    public static function submit(Order $order): void
    {
        $payload = self::buildPayload($order);

        if ($payload === null) {
            return;
        }

        try {
            $response = (new Dfi())->addOrder(
                $order->getReferenceId(),
                $payload['customerData'],
                $payload['products'],
                $payload['options']
            );
            $order->setValue('dfi_addorder_response', json_encode($response));
            $order->setValue('dfi_addorder_sent_at', date('Y-m-d H:i:s'));
        } catch (DfiException $e) {
            \rex_logger::logException($e);
            $order->setValue('dfi_addorder_response', json_encode(['error' => $e->getMessage()]));
        }

        $order->save();
    }

    /**
     * Transmits the order to DFI as a pre-ordine via /v2/pad/addorder.
     * Same success/failure recording behaviour as submit(), but on the
     * dedicated dfi_preorder_sent_at/dfi_preorder_response fields, since a
     * pre-ordine and a (manually resendable) full order are tracked
     * separately.
     */
    public static function submitPreOrder(Order $order): void
    {
        $payload = self::buildPayload($order);

        if ($payload === null) {
            return;
        }

        try {
            $response = (new Dfi())->addPreOrder(
                $order->getReferenceId(),
                $payload['customerData'],
                $payload['products'],
                $payload['options']
            );
            $order->setValue('dfi_preorder_response', json_encode($response));
            $order->setValue('dfi_preorder_sent_at', date('Y-m-d H:i:s'));
        } catch (DfiException $e) {
            \rex_logger::logException($e);
            $order->setValue('dfi_preorder_response', json_encode(['error' => $e->getMessage()]));
        }

        $order->save();
    }

    /**
     * Builds the customer/products/options data shared by both the pre-order
     * and the full order submission. Returns null if the order has no
     * shipping address (both submissions are no-ops in that case).
     *
     * Recalculates shipping_cost/fees live via DFI at submission time rather
     * than reusing the values stored during checkout - submit() can run much
     * later via the manual "resend to DFI" backend button, by which point
     * the checkout-time figures may be stale. shipping_cost reflects what
     * was actually charged (manual value if the manual/DFI toggle resolved
     * to manual), while fees always come from DFI itself.
     *
     * All shop prices (products + shipping) are stored gross. DFI expects
     * net-of-VAT figures instead (total_without_tax per product, and net
     * shipping_cost/fees), using the VAT rate of the invoice address'
     * country - not each product's own configured tax rate, since all
     * prices are assumed to have been entered gross at a uniform rate.
     * `vat` is derived as the remainder (total minus the net figures) rather
     * than read from the order's own tax bucketing, so total = sum(net) +
     * vat always holds exactly for DFI's own cross-check.
     *
     * All net figures are rounded to 3 decimals rather than 2 - DFI sums the
     * per-line/-field values back up on their side, and cent-rounding each
     * one individually before that summation caused their total to drift by
     * a cent (e.g. 46.00 stored as 45.99). The extra decimal keeps enough
     * precision for their sum to land on the actual total.
     */
    private static function buildPayload(Order $order): ?array
    {
        $address = $order->getShippingAddress();

        if (!$address) {
            return null;
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

        $vatRate = self::resolveVatRate($order);

        $products  = [];
        $netTotal  = 0.0;
        foreach ($order->getOrderProducts() as $product) {
            $hasVariant    = (string) $product->getValue('variant_key') !== '';
            $qty           = (int) $product->getValue('cart_quantity');
            $grossLineTotal = (float) $product->getPrice(true) * $qty;
            $netLineTotal   = round($grossLineTotal / (1 + $vatRate / 100), 3);
            $netTotal      += $netLineTotal;

            $products[] = [
                'qty'               => $qty,
                'sku'               => (string) ($hasVariant ? $product->getValue('code') : $product->getValue('ax_code')),
                'total_without_tax' => $netLineTotal,
            ];
        }

        $shipping           = $order->getValue('shipping');
        $shippingCostGross  = (float) $order->getValue('shipping_costs');
        $feesGross          = 0.0;

        if ($shipping instanceof DfiShipping) {
            $shipping->getGrossPrice($order);
            $feesGross         = $shipping->getExciseFees();
            $shippingCostGross = $shipping->getShippingCost();
        }

        $shippingCostNet = round($shippingCostGross / (1 + $vatRate / 100), 3);
        $feesNet         = round($feesGross / (1 + $vatRate / 100), 3);
        $netTotal       += $shippingCostNet + $feesNet;

        $total = (float) $order->getTotal();
        $vat   = round($total - $netTotal, 3);

        return [
            'customerData' => $customerData,
            'products'     => $products,
            'options'      => [
                'country'       => $customerData['country'],
                'insurance'     => false,
                'order_notes'   => trim((string) $order->getValue('remarks')),
                'total'         => $total,
                'vat'           => $vat,
                'shipping_cost' => $shippingCostNet,
                'fees'          => $feesNet,
            ],
        ];
    }

    /**
     * VAT rate to use for the net-of-tax figures sent to DFI, taken from the
     * invoice address' country (rex_prj_country.vat_rate). Falls back to
     * FALLBACK_VAT_RATE if there's no invoice address, no resolvable
     * country, or the country has no rate configured.
     */
    private static function resolveVatRate(Order $order): float
    {
        $invoiceAddress = $order->getInvoiceAddress();
        $Country        = $invoiceAddress ? $invoiceAddress->getCountry() : null;
        $vatRate        = $Country ? $Country->getValue('vat_rate') : null;

        return $vatRate !== null && $vatRate !== '' ? (float) $vatRate : self::FALLBACK_VAT_RATE;
    }
}
