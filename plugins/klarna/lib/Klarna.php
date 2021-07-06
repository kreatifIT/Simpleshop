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

/*
 * Url to Test-Area: https://playground.eu.portal.klarna.com/
 */

namespace FriendsOfREDAXO\Simpleshop;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Kreatif\Project\Settings;
use Sprog\Wildcard;


class Klarna extends PaymentAbstract
{
    const NAME             = 'simpleshop.klarna';
    const LIVE_BASE_URL    = 'https://api.klarna.com/';
    const SANDBOX_BASE_URL = 'https://api.playground.klarna.com/';

    protected $responses = [];

    public function getName()
    {
        if ($this->name == '') {
            $this->name = Wildcard::get(self::NAME);
        }
        return parent::getName();
    }

    protected function getLangCode(\rex_clang $clang = null)
    {
        if (!$clang) {
            $clang = \rex_clang::getCurrent();
        }
        [$lang, $charset] = explode('.', $clang->getValue('setlocale'));
        return str_replace('_', '-', $lang);
    }

    public function initPayment($Order)
    {
        include_once \rex_path::addon('simpleshop', '/vendor/autoload.php');

        $Settings  = \rex::getConfig('simpleshop.Klarna.Settings');
        $test_mode = from_array($Settings, 'use_test_mode', false);
        $base_url  = $test_mode ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
        $alias     = $test_mode ? $Settings['sandbox_alias'] : $Settings['alias'];
        $secret    = $test_mode ? $Settings['sandbox_secret'] : $Settings['secret'];

        if ($alias == '' || $secret == '') {
            throw new KlarnaException('The Klarna Credentials are not set!', 1);
        }

        $currency      = 'EUR';
        $total         = number_format(
            $Order->getValue('total'),
            2,
            '',
            ''
        ); // total payment (including tax + shipping)
        $taxes         = array_sum($Order->getValue('taxes'));
        $tosPage       = Settings::getArticle('tos_page_id');
        $shippingAddr  = $Order->getShippingAddress();
        $country       = $shippingAddr->valueIsset('country') ? Country::get($shippingAddr->getValue('country')) : null;
        $langCode      = $this->getLangCode();
        $shippingCosts = $Order->getValue('shipping_costs');

        if ($shippingCosts > 0) {
            $taxes += $shippingCosts / 122 * 22;
        }

        $jsonBody = [
            'purchase_country'  => $country ? $country->getValue('iso2') : 'IT',
            'purchase_currency' => $currency,
            'locale'            => $langCode,
            'order_amount'      => $total,
            'order_tax_amount'  => number_format($taxes, 2, '', ''),
            'order_lines'       => $this->getOrderLines($Order),
            'merchant_urls'     => [
                'terms'        => html_entity_decode($tosPage->getUrl()),
                'checkout'     => html_entity_decode(
                    rex_getUrl(null, null, ['sid' => '{checkout.order.id}', 'ts' => time()])
                ),
                'confirmation' => html_entity_decode(
                    rex_getUrl(
                        null,
                        null,
                        [
                            'action'   => 'pay-process',
                            'sid'      => '{checkout.order.id}',
                            'order_id' => $Order->getId(),
                            'ts'       => time(),
                        ]
                    )
                ),
                'push'         => html_entity_decode(
                    rex_getUrl(
                        null,
                        null,
                        [
                            'action'   => 'process_ipn',
                            'sid'      => '{checkout.order.id}',
                            'order_id' => $Order->getId(),
                        ]
                    )
                ),
            ],
        ];
        $jsonBody = $this->addBillingAddress($Order, $jsonBody);

        try {
            $client       = new Client();
            $response     = $client->request(
                'POST',
                "{$base_url}/checkout/v3/orders",
                [
                    'auth' => [$alias, $secret],
                    'json' => $jsonBody,
                ]
            );
            $jsonResponse = $response->getBody()->getContents();
        } catch (ClientException $ex) {
            $response     = $ex->getResponse()->getBody()->getContents();
            $responseData = \GuzzleHttp\json_decode($response, true);

            if (\rex_addon::get('project')->getProperty('compile') == 1) {
                pr($jsonBody, 'brown');
            }

            Utils::log('Klarna.initPayment', 'Error-Data: ' . print_r($responseData, true), 'ERROR');

            throw new KlarnaException($responseData['error_messages'][0]);
        }
        $responseData = \GuzzleHttp\json_decode($jsonResponse, true);

        $responses                = (array)$this->getValue('responses');
        $responses['initPayment'] = $jsonResponse;
        $this->setValue('responses', $responses);
        $Order->setValue('payment', Order::prepareData($this));
        $Order->save();

        Utils::log('Klarna.initPayment', 'Data: ' . print_r($responseData, true), 'INFO');

        return $responseData;
    }

    private function addBillingAddress($Order, $klarnaParams)
    {
        $customerData = $Order->getCustomerData();
        $invoiceAddr  = $Order->getInvoiceAddress();

        $klarnaParams['billing_address'] = [
            'given_name'     => $invoiceAddr->getValue('firstname'),
            'family_name'    => $invoiceAddr->getValue('lastname'),
            'email'          => $customerData->getValue('email'),
            'street_address' => $invoiceAddr->getValue('street'),
            'postal_code'    => $invoiceAddr->getValue('postal'),
            'city'           => $invoiceAddr->getValue('location'),
        ];
        return $klarnaParams;
    }

    private function getOrderLines($order)
    {
        $orderLines = [];
        /**@var \FriendsOfREDAXO\Simpleshop\Order $order */
        $orderProducts = $order->getProducts();
        $promotions    = $order->getValue('promotions');
        $shippingCosts = $order->getValue('shipping_costs');

        foreach ($orderProducts as $orderProduct) {
            $product      = $orderProduct->getValue('data');
            $tax          = Tax::get($product->getValue('tax'));
            $quantity     = $orderProduct->getValue('cart_quantity');
            $orderLines[] = [
                'type'             => 'physical',
                'name'             => $product->getName(),
                'quantity'         => $quantity,
                'unit_price'       => number_format($product->getPrice(true), 2, '', ''),
                'tax_rate'         => number_format($tax->getValue('tax'), 2, '', ''),
                'total_amount'     => number_format($product->getPrice(true) * $quantity, 2, '', ''),
                'total_tax_amount' => number_format($product->getTax() * $quantity, 2, '', ''),
            ];
        }

        foreach ($promotions as $promotion) {
            $discount    = $promotion->getValue('value') * -1;
            $netValues   = array_sum($promotion->getValue('net_values')) * -1;
            $taxDiscount = array_sum($promotion->getValue('tax_value')) * -1;
            $percentAge  = 22;

            $orderLines[] = [
                'type'             => 'discount',
                'name'             => $promotion->getName(),
                'quantity'         => 1,
                'unit_price'       => number_format($discount, 2, '', ''),
                'tax_rate'         => number_format($percentAge, 2, '', ''),
                'total_amount'     => number_format($discount, 2, '', ''),
                'total_tax_amount' => number_format($taxDiscount, 2, '', ''),
            ];
        }

        if ($shippingCosts > 0) {
            $shipping     = $order->getValue('shipping');
            $shippingNet  = $shippingCosts / 1.22;
            $orderLines[] = [
                'type'             => 'shipping_fee',
                'name'             => $shipping->getName(),
                'quantity'         => 1,
                'unit_price'       => number_format($shippingCosts, 2, '', ''),
                'tax_rate'         => 2200,
                'total_amount'     => number_format($shippingCosts, 2, '', ''),
                'total_tax_amount' => number_format($shippingNet * 0.22, 2, '', ''),
            ];
        }
        return $orderLines;
    }

    public function processPayment($Order)
    {
        include_once \rex_path::addon('simpleshop', '/vendor/autoload.php');

        $Settings  = \rex::getConfig('simpleshop.Klarna.Settings');
        $test_mode = from_array($Settings, 'use_test_mode', false);
        $base_url  = $test_mode ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
        $alias     = $test_mode ? $Settings['sandbox_alias'] : $Settings['alias'];
        $secret    = $test_mode ? $Settings['sandbox_secret'] : $Settings['secret'];
        $klarnaId  = \rex_request('sid', 'string');


        try {
            $client   = new Client();
            $response = $client->request(
                'GET',
                "{$base_url}/checkout/v3/orders/{$klarnaId}",
                ['auth' => [$alias, $secret]]
            );
        } catch (ClientException $ex) {
            $response     = $ex->getResponse()->getBody()->getContents();
            $responseData = \GuzzleHttp\json_decode($response, true);

            Utils::log('Klarna.processPayment', 'Error-Data: ' . print_r($responseData, true), 'ERROR');

            throw new KlarnaException($ex->getMessage());
        }

        $jsonResponse = $response->getBody()->getContents();
        $responseData = \GuzzleHttp\json_decode($jsonResponse, true);

        $responses                = (array)$this->getValue('responses');
        $responses['pay-process'] = $jsonResponse;
        $this->setValue('responses', $responses);
        $Order->setValue('payment', Order::prepareData($this));

        if ($responseData['status'] == 'checkout_complete') // log successful payment
        {
            Utils::log('Klarna.processPayment', 'successfull', 'INFO');
            $Order->setValue('status', 'IP');
            $Order->save();
        } else {
            $Order->save();

            $msg = \rex_request('messaggio', 'string');
            Utils::log('Klarna.processPayment', 'Payment failed', 'ERROR');

            throw new KlarnaException($msg);
        }
    }

    public function processIPN($Order, $postData)
    {
        include_once \rex_path::addon('simpleshop', '/vendor/autoload.php');

        $Settings  = \rex::getConfig('simpleshop.Klarna.Settings');
        $test_mode = from_array($Settings, 'use_test_mode', false);
        $base_url  = $test_mode ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
        $alias     = $test_mode ? $Settings['sandbox_alias'] : $Settings['alias'];
        $secret    = $test_mode ? $Settings['sandbox_secret'] : $Settings['secret'];
        $klarnaId  = \rex_request('sid', 'string');
        $responses = (array)$this->getValue('responses');


        try {
            $client   = new Client();
            $response = $client->request(
                'GET',
                "{$base_url}/ordermanagement/v1/orders/{$klarnaId}",
                ['auth' => [$alias, $secret]]
            );
        } catch (ClientException $ex) {
            $response     = $ex->getResponse()->getBody()->getContents();
            $responseData = \GuzzleHttp\json_decode($response, true);

            Utils::log('Klarna.processIPN', 'Error-Data: ' . print_r($responseData, true), 'ERROR');

            throw new KlarnaException($ex->getMessage());
        }

        $responses['process_ipn'] = $response->getBody()->getContents();

        try {
            $client   = new Client();
            $response = $client->request(
                'POST',
                "{$base_url}/ordermanagement/v1/orders/{$klarnaId}/acknowledge",
                ['auth' => [$alias, $secret], 'headers' => ['Content-Type' => 'application/json']]
            );
        } catch (ClientException $ex) {
            $response     = $ex->getResponse()->getBody()->getContents();
            $responseData = \GuzzleHttp\json_decode($response, true);

            Utils::log('Klarna.acknowledge', 'Error-Data: ' . print_r($responseData, true), 'ERROR');

            throw new KlarnaException($ex->getMessage());
        }

        $responses['acknowledge'] = $response->getBody()->getContents();

        $this->setValue('responses', $responses);
        $Order->setValue('payment', Order::prepareData($this));
        $Order->save();

        Utils::log('Klarna.process_ipn', 'successfull', 'INFO');
    }
}

class KlarnaException extends \Exception
{
}