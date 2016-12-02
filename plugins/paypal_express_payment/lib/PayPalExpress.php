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

use Sprog\Wildcard;

class PayPalExpress extends PaymentAbstract
{
    const NAME                 = 'shop.paypal_express';
    const API_VERSION          = '124.0';
    const SANDBOX_BASE_URL     = 'https://api-3t.sandbox.paypal.com/nvp/';
    const LIVE_BASE_URL        = 'https://api-3t.paypal.com/nvp/';
    const LIVE_REDIRECT_URL    = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
    const SANDBOX_REDIRECT_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';

    protected $responses = [];

    public function getName()
    {
        if ($this->name == '')
        {
            $this->name = checkstr(Wildcard::get(self::NAME), self::NAME);
        }
        return parent::getName();
    }

    public function initPayment($order_id, $total_amount, $order_descr, $show_cc = FALSE)
    {
        $use_sandbox = \rex::getProperty('use_paypal_sandbox');
        $key         = $use_sandbox ? 'paypal_credentials_sandbox' : 'paypal_credentials_live';
        $url         = $use_sandbox ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
        $credentials = \rex::getProperty($key);

        if (!$credentials || empty ($credentials['signature']))
        {
            $msg = '
                The property "' . $key . '" is not set or not correct! It should contain your paypal credentials. Please define them in your Project-Addon like: 
                rex::setProperty(\'' . $key . '\', [
                    \'user\'      => \'XXXX\',
                    \'pwd\'       => \'XXXXXX\',
                    \'signature\' => \'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX\',
                ]);
            ';
            throw new PaypalException($msg);
        }

        $data = [
            'METHOD'  => 'SetExpressCheckout',
            'VERSION' => self::API_VERSION,

            'USER'      => $credentials['user'],
            'PWD'       => $credentials['pwd'],
            'SIGNATURE' => $credentials['signature'],

            'returnUrl' => rex_getFullUrl(NULL, NULL, ['action' => 'pay_process']),
            'cancelUrl' => rex_getFullUrl(NULL, NULL, ['action' => 'cancelled']),

            'PAYMENTREQUEST_0_PAYMENTREQUESTID' => $order_id,
            'PAYMENTREQUEST_0_AMT'              => $total_amount, // total payment (including tax + shipping)
            'PAYMENTREQUEST_0_CURRENCYCODE'     => 'EUR',
            'L_PAYMENTREQUEST_0_NAME0'          => $order_descr,
            'L_PAYMENTREQUEST_0_AMT0'           => $total_amount,
            'NOSHIPPING'                        => 1,
            'ALLOWNOTE'                         => 0,
            'L_PAYMENTTYPE0'                    => 'InstantOnly',

            'HDRIMG'          => '', // URL: image on the top; max: 750x90px
            'LOGOIMG'         => '', // HTTPS-URL: logo; max: 190x60px
            'CARTBORDERCOLOR' => '',

            'EMAIL'       => '', // customer email prefilled
            'LANDINGPAGE' => $show_cc ? 'Billing' : 'Login', // shows either credit card form OR Paypal Login page
        ];

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Paypal.initPaymentData', $data));

        $sdata     = html_entity_decode(urldecode(http_build_query($data)));
        $Connector = new WSConnector($url);
        $Connector->setRespFormat('text/html');
        $response = $Connector->request('', $sdata, 'post', 'Paypal.initPayment');

        parse_str(urldecode($response['response']), $__response);

        $logMsg = "
            {$__response['L_LONGMESSAGE0']}
            Data: " . print_r($data, TRUE) . "
            Response: " . print_r($__response, TRUE) . "
        ";
        if ($__response['ACK'] != 'Success')
        {
            Utils::log('Paypal.initPayment.response', $logMsg, 'ERROR', TRUE);
            throw new PaypalException($__response['L_LONGMESSAGE0'], $__response['L_ERRORCODE0']);
        }
        else
        {
            Utils::log('Paypal.initPayment.response', $logMsg, 'INFO');
            $this->responses['initPayment'] = $__response;
            $Order                          = Session::getCurrentOrder();
            $Order->setValue('payment', $this);
        }
        // redirect to paypal
        return ($use_sandbox ? self::SANDBOX_REDIRECT_URL : self::LIVE_REDIRECT_URL) . $__response['TOKEN'];
    }

    public function processPayment($token, $payer_id, $total_amount)
    {
        $use_sandbox = \rex::getProperty('use_paypal_sandbox');
        $key         = $use_sandbox ? 'paypal_credentials_sandbox' : 'paypal_credentials_live';
        $credentials = \rex::getProperty($key);
        $url         = $use_sandbox ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;

        $data = [
            'METHOD'   => 'DoExpressCheckoutPayment',
            'VERSION'  => self::API_VERSION,
            'TOKEN'    => $token,
            'PAYERID'  => $payer_id,
            'MSGSUBID' => $payer_id,

            'USER'      => $credentials['user'],
            'PWD'       => $credentials['pwd'],
            'SIGNATURE' => $credentials['signature'],

            'PAYMENTREQUEST_0_AMT'          => $total_amount,
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
        ];

        $sdata     = html_entity_decode(urldecode(http_build_query($data)));
        $Connector = new WSConnector($url);
        $Connector->setRespFormat('text/html');
        $response = $Connector->request('', $sdata, 'post', 'Paypal.processPayment');

        parse_str(urldecode($response['response']), $__response);

        $logMsg = "
            {$__response['L_LONGMESSAGE0']}
            Data: " . print_r($data, TRUE) . "
            Response: " . print_r($__response, TRUE) . "
        ";

        if ($__response['ACK'] != 'Success')
        {

            Utils::log('Paypal.processPayment.response', $logMsg, 'ERROR', TRUE);
            throw new PaypalException($__response['L_LONGMESSAGE0'], $__response['L_ERRORCODE0']);
        }
        if ($__response['PAYMENTINFO_0_PAYMENTSTATUS'] != 'Completed')
        {
            $logMsg = "
                The Payment with Transaction-ID = {$__response['PAYMENTINFO_0_TRANSACTIONID']} "
                . "has status = '{$__response['PAYMENTINFO_0_PAYMENTSTATUS']}'
            ";
            Utils::log('Paypal.processPayment.response', $logMsg, 'ERROR', TRUE);
            throw new PaypalException($__response['L_LONGMESSAGE0'], $__response['L_ERRORCODE0']);
        }
        else
        {
            // log successful payment
            Utils::log('Paypal.processPayment.response', $logMsg, 'INFO');
            $this->responses['processPayment'] = $__response;
            $Order                             = Session::getCurrentOrder();
            $Order->setValue('payment', $this);
            // update status
            $Order->setValue('status', 'IP');
        }
        return $__response;
    }
}

class PaypalException extends \Exception
{
}