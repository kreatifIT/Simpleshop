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
    const LIVE_BASE_URL    = 'https://api.klarna.com';
    const SANDBOX_BASE_URL = 'https://api.playground.klarna.com';

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

    public function createSession()
    {
        $_responses      = [];
        $order           = Session::getCurrentOrder();
        $_payment        = $order->getValue('payment');
        $isSessionUpdate = isset($_responses['createSession']) && isset($_responses['createSession']['client_token']);

        if ($_payment instanceof self) {
            $_responses = $_payment->getValue('responses');
        }

        if ($isSessionUpdate) {
            $path = "/payments/v1/sessions/{$_responses['createSession']['session_id']}";
        } else {
            $path = '/payments/v1/sessions';
        }

        include_once \rex_path::addon('simpleshop', '/vendor/autoload.php');

        $Settings  = \rex::getConfig('simpleshop.Klarna.Settings');
        $test_mode = from_array($Settings, 'use_test_mode', false);
        $base_url  = $test_mode ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
        $alias     = $test_mode ? $Settings['sandbox_alias'] : $Settings['alias'];
        $secret    = $test_mode ? $Settings['sandbox_secret'] : $Settings['secret'];

        if ($alias == '' || $secret == '') {
            throw new KlarnaException('The Klarna Credentials are not set!', 1);
        }

        $currency     = 'EUR';
        $total        = 1.22;
        $taxes        = $total / 122 * 22;
        $shippingAddr = $order->getShippingAddress();
        $country      = $shippingAddr->valueIsset('country') ? Country::get($shippingAddr->getValue('country')) : null;
        $langCode     = $this->getLangCode();

        $jsonBody = [
            'purchase_country'  => $country ? $country->getValue('iso2') : 'IT',
            'purchase_currency' => $currency,
            'locale'            => $langCode,
            'order_amount'      => number_format($total, 2, '', ''),
            'order_tax_amount'  => number_format($taxes, 2, '', ''),
            'order_lines'       => [
                [
                    'type'             => 'physical',
                    'name'             => 'Create Session',
                    'quantity'         => 1,
                    'unit_price'       => number_format($total, 2, '', ''),
                    'tax_rate'         => number_format(22, 2, '', ''),
                    'total_amount'     => number_format($total, 2, '', ''),
                    'total_tax_amount' => number_format($taxes, 2, '', ''),
                ],
            ],
        ];
        $jsonBody = $this->addBillingAddress($order, $jsonBody);

        try {
            $client       = new Client();
            $response     = $client->request('POST', $base_url . $path, [
                'auth' => [$alias, $secret],
                'json' => $jsonBody,
            ]);
            $jsonResponse = $response->getBody()
                ->getContents();
        } catch (ClientException $ex) {
            $response     = $ex->getResponse()
                ->getBody()
                ->getContents();
            $responseData = \GuzzleHttp\json_decode($response, true);

            Utils::log('Klarna.createSession', 'Error-Data: ' . print_r($responseData, true), 'ERROR');

            if ($responseData['error_code'] == 'NOT_FOUND') {
                Utils::log('Klarna.createSession', 'session-id = "' . $_responses['createSession']['session_id'] . '" not found', 'INFO');
                $this->setValue('responses', []);
                $order->setValue('payment', Order::prepareData($this));
                Session::setCheckoutData('Order', $order);
                throw new KlarnaException($responseData['error_messages'][0], 100);
            } else {
                throw new KlarnaException($responseData['error_messages'][0]);
            }
        }

        if ($isSessionUpdate) {
            $responseData = $_responses['createSession'];
        } else {
            $responseData = json_decode($jsonResponse, true);
        }

        $responses                  = (array)$this->getValue('responses');
        $responses['createSession'] = $responseData;
        $this->setValue('responses', $responses);
        $order->setValue('payment', Order::prepareData($this));
        $order->save();


        Utils::log('Klarna.createSession', 'Data: ' . print_r($responseData, true), 'INFO');
        Session::setCheckoutData('Order', $order);

        return $responseData;
    }

    public function placeOrder($Order)
    {
        include_once \rex_path::addon('simpleshop', '/vendor/autoload.php');

        $Settings      = \rex::getConfig('simpleshop.Klarna.Settings');
        $test_mode     = from_array($Settings, 'use_test_mode', false);
        $base_url      = $test_mode ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
        $alias         = $test_mode ? $Settings['sandbox_alias'] : $Settings['alias'];
        $secret        = $test_mode ? $Settings['sandbox_secret'] : $Settings['secret'];
        $authResponses = $this->getValue('responses')['authResponse'];

        if ($alias == '' || $secret == '') {
            throw new KlarnaException('The Klarna Credentials are not set!', 1);
        } else if (!isset($authResponses['authorization_token'])) {
            throw new KlarnaException('The authorization token is missing ', 3);
        }

        $currency      = 'EUR';
        $total         = number_format($Order->getValue('total'), 2, '', ''); // total payment (including tax + shipping)
        $taxes         = array_sum($Order->getValue('taxes'));
        $shippingAddr  = $Order->getShippingAddress();
        $country       = $shippingAddr->valueIsset('country') ? Country::get($shippingAddr->getValue('country')) : null;
        $langCode      = $this->getLangCode();

        $jsonBody = [
            'purchase_country'  => $country ? $country->getValue('iso2') : 'IT',
            'purchase_currency' => $currency,
            'locale'            => $langCode,
            'order_amount'      => $total,
            'order_tax_amount'  => number_format($taxes, 2, '', ''),
            'order_lines'       => $this->getOrderLines($Order),
        ];
        $jsonBody = $this->addBillingAddress($Order, $jsonBody);

        try {
            $client       = new Client();
            $response     = $client->request('POST', "{$base_url}/payments/v1/authorizations/{$authResponses['authorization_token']}/order", [
                'auth' => [$alias, $secret],
                'json' => $jsonBody,
            ]);
            $jsonResponse = $response->getBody()
                ->getContents();
        } catch (ClientException $ex) {
            $response     = $ex->getResponse()
                ->getBody()
                ->getContents();
            $responseData = \GuzzleHttp\json_decode($response, true);

            Utils::log('Klarna.placeOrder', 'Error-Data: ' . print_r($responseData, true), 'ERROR');

            throw new KlarnaException($responseData['error_messages'][0]);
        }
        $responseData = \GuzzleHttp\json_decode($jsonResponse, true);

        $responses               = (array)$this->getValue('responses');
        $responses['placeOrder'] = $jsonResponse;
        $this->setValue('responses', $responses);
        $Order->setValue('payment', Order::prepareData($this));
        $Order->save();

        Utils::log('Klarna.placeOrder', 'Data: ' . print_r($responseData, true), 'INFO');

        return $responseData;
    }

    private function addBillingAddress($Order, $klarnaParams)
    {
        $customerData = $Order->getCustomerData();
        $invoiceAddr  = $Order->getInvoiceAddress();
        $country      = $invoiceAddr->valueIsset('country') ? Country::get($invoiceAddr->getValue('country')) : null;

        $klarnaParams['billing_address'] = [
            'given_name'     => $invoiceAddr->getValue('firstname'),
            'family_name'    => $invoiceAddr->getValue('lastname'),
            'email'          => $customerData->getValue('email'),
            'street_address' => $invoiceAddr->getValue('street'),
            'postal_code'    => $invoiceAddr->getValue('postal'),
            'city'           => $invoiceAddr->getValue('location'),
            'country'        => $country ? $country->getValue('iso2') : 'IT',
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

    public function captureOrder($Order)
    {
        include_once \rex_path::addon('simpleshop', '/vendor/autoload.php');

        $Settings       = \rex::getConfig('simpleshop.Klarna.Settings');
        $test_mode      = from_array($Settings, 'use_test_mode', false);
        $base_url       = $test_mode ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
        $alias          = $test_mode ? $Settings['sandbox_alias'] : $Settings['alias'];
        $secret         = $test_mode ? $Settings['sandbox_secret'] : $Settings['secret'];
        $placeOrderResp = $this->getValue('responses')['placeOrder'];

        if (!isset($placeOrderResp['order_id'])) {
            Utils::log('Klarna.processPayment', 'order_id is not set', 'ERROR');
        }

        try {
            $client   = new Client();
            $response = $client->request('GET', "{$base_url}/checkout/v3/orders/{$placeOrderResp['order_id']}", ['auth' => [$alias, $secret]]);
        } catch (ClientException $ex) {
            $response = $ex->getResponse()
                ->getBody()
                ->getContents();

            $responseData = \GuzzleHttp\json_decode($response, true);

            Utils::log('Klarna.processPayment', 'Error-Data: ' . print_r($responseData, true), 'ERROR');

            throw new KlarnaException($ex->getMessage());
        }

        $jsonResponse = $response->getBody()
            ->getContents();
        $responseData = \GuzzleHttp\json_decode($jsonResponse, true);

        $responses                   = (array)$this->getValue('responses');
        $responses['processPayment'] = $jsonResponse;
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
            $response = $client->request('GET', "{$base_url}/ordermanagement/v1/orders/{$klarnaId}", ['auth' => [$alias, $secret]]);
        } catch (ClientException $ex) {
            $response     = $ex->getResponse()
                ->getBody()
                ->getContents();
            $responseData = \GuzzleHttp\json_decode($response, true);

            Utils::log('Klarna.processIPN', 'Error-Data: ' . print_r($responseData, true), 'ERROR');

            throw new KlarnaException($ex->getMessage());
        }

        $responses['process_ipn'] = $response->getBody()
            ->getContents();

        try {
            $client   = new Client();
            $response = $client->request('POST', "{$base_url}/ordermanagement/v1/orders/{$klarnaId}/acknowledge", ['auth' => [$alias, $secret], 'headers' => ['Content-Type' => 'application/json']]);
        } catch (ClientException $ex) {
            $response     = $ex->getResponse()
                ->getBody()
                ->getContents();
            $responseData = \GuzzleHttp\json_decode($response, true);

            Utils::log('Klarna.acknowledge', 'Error-Data: ' . print_r($responseData, true), 'ERROR');

            throw new KlarnaException($ex->getMessage());
        }

        $responses['acknowledge'] = $response->getBody()
            ->getContents();

        $this->setValue('responses', $responses);
        $Order->setValue('payment', Order::prepareData($this));
        $Order->save();

        Utils::log('Klarna.process_ipn', 'successfull', 'INFO');
    }
}

class KlarnaException extends \Exception
{
}