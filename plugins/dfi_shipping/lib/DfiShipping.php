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
        $this->price = $this->calculatePrice($order, $products ?: $order->getOrderProducts());
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
            return (float) $order->getValue('shipping_costs');
        }

        $Country  = $address->getCountry();
        $country  = $Country ? $Country->getValue('iso2') : '';
        $postcode = $address->getValue('postal');

        if (!$country || !$postcode) {
            return (float) $order->getValue('shipping_costs');
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
            $this->fees        = self::$cache[$cacheKey]['fees'];
            $this->apiResponse = self::$cache[$cacheKey]['response'];
            return self::$cache[$cacheKey]['price'];
        }

        try {
            $dfi      = new Dfi();
            $response = $dfi->estimateShipping($country, $postcode, $dfiProducts, false, 0.0, $cartTotal);
            $this->fees        = (float) ($response->fees ?? 0);
            $this->apiResponse = (array) $response;
            $price             = (float) $response->shipping_cost + $this->fees;

            self::$cache[$cacheKey] = ['price' => $price, 'fees' => $this->fees, 'response' => $this->apiResponse];

            return $price;
        } catch (DfiException $e) {
            \rex_logger::logException($e);
            $this->fees        = 0.0;
            $this->apiResponse = null;
            return (float) $order->getValue('shipping_costs');
        }
    }
}
