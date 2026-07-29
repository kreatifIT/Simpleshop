<?php

namespace FriendsOfREDAXO\Simpleshop;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Dfi
{
    const BASE_URL = 'https://dfiapi.plugindfi.com';

    private $client;

    public function __construct()
    {
        $settings = \rex::getConfig('simpleshop.DfiShipping.Settings', []);
        $token    = from_array($settings, 'api_token');

        $this->client = new Client([
            'base_uri' => self::BASE_URL,
            'headers'  => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'timeout'  => 30,
        ]);
    }

    /**
     * @param string $country   ISO2 country code (e.g. "DE")
     * @param string $postcode
     * @param array  $products  [['qty' => int, 'sku' => string], ...]
     * @param bool   $insurance
     * @param float  $insuranceValue
     * @param float  $cartTotal
     * @return object
     * @throws DfiException
     */
    public function estimateShipping(
        string $country,
        string $postcode,
        array $products,
        bool $insurance = false,
        float $insuranceValue = 0.0,
        float $cartTotal = 0.0
    ): object {
        $payload = [
            'country'         => $country,
            'postcode'        => $postcode,
            'insurance'       => $insurance,
            'insurance_value' => $insuranceValue,
            'cart_total'      => $cartTotal,
            'products'        => $products,
        ];
        pr($payload);

        return $this->post('/v2/shipping', $payload);
    }

    /**
     * @param string $orderId
     * @param array  $customerData  {customer, address, city, state, postcode, country, phone, email}
     * @param array  $products      [['qty' => int, 'sku' => string, 'total_without_tax' => float], ...]
     * @param array  $options       {insurance, order_notes, parcels_info, total, vat, shipping_cost, fees, country}
     * @return object
     * @throws DfiException
     */
    public function addOrder(
        string $orderId,
        array $customerData,
        array $products,
        array $options = []
    ): object {
        $payload = array_merge([
            'order_id'      => $orderId,
            'customer_data' => $customerData,
            'products'      => $products,
            'insurance'     => false,
            'order_notes'   => '',
            'parcels_info'  => ['parcels' => 1, 'details' => []],
            'total'         => 0.0,
            'vat'           => 0.0,
            'shipping_cost' => 0.0,
            'fees'          => 0.0,
        ], $options);

        return $this->post('/v2/addorder', $payload);
    }

    private function post(string $path, array $payload): object
    {
        try {
            $response = $this->client->post($path, ['json' => $payload]);
            $body     = json_decode((string) $response->getBody());

            if ($body === null) {
                throw new DfiException("DFI API: invalid JSON response for {$path}");
            }

            return $body;
        } catch (GuzzleException $e) {
            \rex_logger::logException($e);
            throw new DfiException(
                "DFI API request failed [{$path}]: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
