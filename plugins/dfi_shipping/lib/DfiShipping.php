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

use Sprog\Wildcard;

class DfiShipping extends ShippingAbstract
{
    const NAME = 'simpleshop.dfi_shipping';

    protected $tax_percentage = 22;
    protected $fees           = 0.0;
    protected $apiResponse    = null;

    /**
     * The shipping cost portion actually charged (manual value if the
     * manual/DFI toggle resolved to manual, DFI's own shipping_cost
     * otherwise) - excluding fees, unlike getGrossPrice()'s combined total.
     * Used when submitting the order to DFI, so DFI is told what was really
     * charged rather than always its own calculated figure.
     */
    protected $shippingCost = 0.0;

    /**
     * Request-level cache keyed by calculation input. Order::getValue('shipping')
     * reconstructs a fresh ShippingAbstract instance on every call (Kreatif Model's
     * class/data unprepareValue), so per-instance caching of $price/$fees doesn't
     * survive between the repeated calculateDocument() runs within one request.
     * A static cache does, avoiding redundant/inconsistent live API calls.
     */
    protected static $cache = [];

    public function getName()
    {
        if ($this->name == '') {
            $this->name = Wildcard::get(self::NAME);
        }
        return parent::getName();
    }

    public function getGrossPrice(Order $order, $products = null)
    {
        $this->price = $this->resolvePrice($order, $products ?: $order->getOrderProducts());
        return $this->price;
    }

    public function getNetPrice(Order $order, $products = null)
    {
        $price = $this->getGrossPrice($order, $products);
        return $price / (100 + $this->tax_percentage) * 100;
    }

    /**
     * The DFI response's "fees" field (excise duty / Akzise), captured as a
     * side effect of the last calculatePrice() call so it can be shown as its
     * own line next to the shipping costs, which already include it.
     */
    public function getExciseFees(): float
    {
        return $this->fees;
    }

    /**
     * Raw /v2/shipping response of the last calculatePrice() call (or cache
     * hit), so the order can record exactly what DFI returned.
     */
    public function getApiResponse()
    {
        return $this->apiResponse;
    }

    /**
     * The shipping cost portion of the last resolvePrice() call, excluding
     * fees - the manually configured value when the manual/DFI toggle
     * resolved to manual, DFI's own shipping_cost otherwise.
     */
    public function getShippingCost(): float
    {
        return $this->shippingCost;
    }

    /**
     * Decides, based on the backend settings (simpleshop.DfiShipping.Settings),
     * whether the price for this order comes from a manually configured value
     * or from a live DFI calculation - mirroring DefaultShipping's country/
     * min-order-tier lookup, but with a per-tier "use_dfi" toggle:
     *
     *   - a per-country tier matching the order's subtotal, with use_dfi off
     *     -> its manually configured cost + DFI's fees
     *   - a per-country tier matching the order's subtotal, with use_dfi on
     *     -> live DFI calculation (shipping_cost + fees)
     *   - no matching country config, but subtotal reaches general_free_shipping
     *     -> free shipping (0) + DFI's fees
     *   - no matching country config, general_use_dfi on -> live DFI calculation
     *   - no matching country config, general_use_dfi off -> general_costs + DFI's fees
     *
     * The "fees" (excise duty / Akzise) always come from DFI regardless of the
     * manual/DFI toggle - it's a tax obligation, not a shipping-price choice,
     * so calculatePrice() runs on every path to fetch it (its own request-level
     * cache keeps this to a single live call either way).
     *
     * Tiers are evaluated in the order they're configured (like DefaultShipping),
     * so they must be entered highest-min_order-first in the settings table.
     */
    protected function resolvePrice(Order $order, $products)
    {
        $Settings  = \rex::getConfig('simpleshop.DfiShipping.Settings', []);
        $countryId = $this->getValue('country_id');

        if (!$countryId) {
            $address   = $order->getShippingAddress();
            $countryId = $address ? $address->getValue('country') : null;
        }

        $customer = $order->getCustomerData();
        $total    = $order->getSubtotal(!$customer->isTaxFree());

        if ($countryId && isset($Settings['costs'][$countryId])) {
            foreach ($Settings['costs'][$countryId] as $minOrder => $tier) {
                if ($total >= $minOrder) {
                    $dfiPrice = $this->calculatePrice($order, $products);

                    if (!empty($tier['use_dfi'])) {
                        return $dfiPrice;
                    }
                    $this->shippingCost = (float) ($tier['cost'] ?? 0);
                    return $this->shippingCost + $this->fees;
                }
            }
        }

        $freeShipping = (float) ($Settings['general_free_shipping'] ?? 0);

        if ($freeShipping > 0 && $total >= $freeShipping) {
            $this->calculatePrice($order, $products);
            $this->shippingCost = 0.0;
            return 0.0 + $this->fees;
        }

        $dfiPrice = $this->calculatePrice($order, $products);

        if (!empty($Settings['general_use_dfi'])) {
            return $dfiPrice;
        }

        $this->shippingCost = (float) ($Settings['general_costs'] ?? 0);
        return $this->shippingCost + $this->fees;
    }

    /**
     * Calculates the shipping price via the DFI API, based on the shipping
     * address' country/postcode. SKU is rex_shop_product_has_feature.code for
     * products with a variant, or rex_shop_product.ax_code for products
     * without one (applyVariantData() only sets variant_key when a variant
     * was applied, so its absence marks a variant-less product).
     * Falls back to the order's previously stored shipping_costs on any error
     * (missing address, API failure) so checkout is never blocked by the API.
     */
    protected function calculatePrice(Order $order, $products)
    {
        $address = $order->getShippingAddress();

        if (!$address || count($products) < 1) {
            $this->shippingCost = (float) $order->getValue('shipping_costs');
            return $this->shippingCost;
        }

        $Country  = $address->getCountry();
        $country  = $Country ? $Country->getValue('iso2') : '';
        $postcode = $address->getValue('postal');

        if (!$country || !$postcode) {
            $this->shippingCost = (float) $order->getValue('shipping_costs');
            return $this->shippingCost;
        }

        $dfiProducts = [];
        foreach ($products as $product) {
            $hasVariant = (string) $product->getValue('variant_key') !== '';
            $dfiProducts[] = [
                'qty' => (int) $product->getValue('cart_quantity'),
                'sku' => (string) ($hasVariant ? $product->getValue('code') : $product->getValue('ax_code')),
            ];
        }

        $cartTotal = round((float) $order->getGrossTotal(), 2);
        $cacheKey  = md5(json_encode([$country, $postcode, $dfiProducts, $cartTotal]));

        if (isset(self::$cache[$cacheKey])) {
            $this->fees         = self::$cache[$cacheKey]['fees'];
            $this->apiResponse  = self::$cache[$cacheKey]['response'];
            $this->shippingCost = self::$cache[$cacheKey]['shippingCost'];
            return self::$cache[$cacheKey]['price'];
        }

        try {
            $dfi      = new Dfi();
            $response = $dfi->estimateShipping($country, $postcode, $dfiProducts, false, 0.0, $cartTotal);
            $this->fees         = (float) ($response->fees ?? 0);
            $this->apiResponse  = (array) $response;
            $this->shippingCost = (float) $response->shipping_cost;
            $price              = $this->shippingCost + $this->fees;

            self::$cache[$cacheKey] = [
                'price'        => $price,
                'fees'         => $this->fees,
                'response'     => $this->apiResponse,
                'shippingCost' => $this->shippingCost,
            ];

            return $price;
        } catch (DfiException $e) {
            \rex_logger::logException($e);
            $this->fees         = 0.0;
            $this->apiResponse  = null;
            $this->shippingCost = (float) $order->getValue('shipping_costs');
            return $this->shippingCost;
        }
    }
}
