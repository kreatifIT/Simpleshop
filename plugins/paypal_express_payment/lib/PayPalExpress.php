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
    const NAME                 = 'label.paypal_express';
    const API_VERSION          = '124.0';
    const SANDBOX_BASE_URL     = 'https://api-3t.sandbox.paypal.com/nvp/';
    const LIVE_BASE_URL        = 'https://api-3t.paypal.com/nvp/';
    const LIVE_REDIRECT_URL    = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
    const SANDBOX_REDIRECT_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';

    protected $responses = [];

    public function getName()
    {
        if ($this->name == '') {
            $this->name = Wildcard::get(self::NAME);
        }
        return parent::getName();
    }

    public function getOrderByPaymentToken()
    {
        $order = null;

        if (rex_get('listener', 'string') == 'ipn') {
            $txnId = trim(rex_request('txn_id', 'string'));

            if ($txnId != '') {
                $stmt = Order::query();
                $stmt->where('payment', "%{$txnId}%", 'LIKE');
                $collection = $stmt->find();

                foreach ($collection as $item) {
                    $payment = $item->getValue('payment');
                    $reponse = $payment->getValue('responses')['processPayment'];

                    if ($reponse['PAYMENTINFO_0_TRANSACTIONID'] == $txnId) {
                        $order = $item;
                        break;
                    }
                }
            }
        } else {
            $token = trim(rex_request('token', 'string'));

            if ($token != '') {
                $stmt = Order::query();
                $stmt->where('payment', "%{$token}%", 'LIKE');
                $collection = $stmt->find();

                foreach ($collection as $item) {
                    $payment = $item->getValue('payment');
                    $reponse = $payment->getValue('responses')['initPayment'];

                    if ($reponse['TOKEN'] == $token) {
                        $order = $item;
                        break;
                    }
                }
            }
        }
        return $order;
    }

    public function initPayment($order_id, $total_amount, $order_descr, $show_cc = false)
    {
        $Settings        = \rex::getConfig('simpleshop.PaypalExpress.Settings');
        $shopConfig      = \rex::getConfig('simpleshop.Settings');
        $prefix          = from_array($Settings, 'api_type', '');
        $url             = $prefix == 'sandbox_' ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
        $api_user        = from_array($Settings, $prefix . 'username', '');
        $api_pwd         = from_array($Settings, $prefix . 'password', '');
        $api_signature   = from_array($Settings, $prefix . 'signature', '');
        $checkoutArticle = \rex_article::get($shopConfig['linklist']['checkout']);


        if ($api_signature == '' || $api_pwd == '' || $api_user == '') {
            throw new PaypalException('The Paypal credentials are not set!');
        }

        $data = [
            'METHOD'  => 'SetExpressCheckout',
            'VERSION' => self::API_VERSION,

            'USER'      => $api_user,
            'PWD'       => $api_pwd,
            'SIGNATURE' => $api_signature,

            'returnUrl'            => $checkoutArticle->getUrl(['action' => 'pay_process', 'ts' => time()]),
            'cancelUrl'            => $checkoutArticle->getUrl(['action' => 'cancelled', 'ts' => time()]),
            'ipn_notification_url' => $checkoutArticle->getUrl(['action' => 'pay_process', 'listener' => 'ipn']),

            'PAYMENTREQUEST_0_PAYMENTREQUESTID' => $order_id,
            'PAYMENTREQUEST_0_AMT'              => (float)number_format($total_amount, 2, '.', ''), // total payment (including tax + shipping)
            'PAYMENTREQUEST_0_CURRENCYCODE'     => 'EUR',
            'L_PAYMENTREQUEST_0_NAME0'          => $order_descr,
            'L_PAYMENTREQUEST_0_AMT0'           => (float)number_format($total_amount, 2, '.', ''),
            'NOSHIPPING'                        => 1,
            'ALLOWNOTE'                         => 0,
            'L_PAYMENTTYPE0'                    => 'InstantOnly',

            'HDRIMG'          => '', // URL: image on the top; max: 750x90px
            'LOGOIMG'         => '', // HTTPS-URL: logo; max: 190x60px
            'CARTBORDERCOLOR' => '',

            'EMAIL'       => '', // customer email prefilled
            'LANDINGPAGE' => $show_cc ? 'Billing' : 'Login', // shows either credit card form OR Paypal Login page
        ];

        $data = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Paypal.initPaymentData', $data, [
            'orderId' => $order_id,
        ]));

        $sdata     = html_entity_decode(urldecode(http_build_query($data)));
        $Connector = new WSConnector($url);
        $Connector->setRespFormat('text/html');
        $response = $Connector->request('', $sdata, 'post', 'Paypal.initPayment');

        parse_str(urldecode($response['response']), $__response);

        $logMsg = "
            {$__response['L_LONGMESSAGE0']}
            Data: " . print_r($data, true) . "
            Response: " . print_r($__response, true) . "
        ";
        if ($__response['ACK'] != 'Success') {
            Utils::log('Paypal.initPayment.response', $logMsg, 'ERROR', true);
            throw new PaypalException($__response['L_LONGMESSAGE0'], $__response['L_ERRORCODE0']);
        } else {
            Utils::log('Paypal.initPayment.response', $logMsg, 'INFO');
            $this->responses['initPayment'] = $__response;
            $Order                          = Session::getCurrentOrder();
            $Order->setValue('payment', $this);
            Session::setCheckoutData('Order', $Order);
            $Order->save();
        }
        // redirect to paypal
        return ($prefix == 'sandbox_' ? self::SANDBOX_REDIRECT_URL : self::LIVE_REDIRECT_URL) . $__response['TOKEN'];
    }

    public function processPayment($token, $payer_id, $order)
    {
        $Settings      = \rex::getConfig('simpleshop.PaypalExpress.Settings');
        $prefix        = from_array($Settings, 'api_type', '');
        $url           = $prefix == 'sandbox_' ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
        $api_user      = from_array($Settings, $prefix . 'username', '');
        $api_pwd       = from_array($Settings, $prefix . 'password', '');
        $api_signature = from_array($Settings, $prefix . 'signature', '');

        if ($order instanceof Order) {
            $totalAmount = $order->getValue('total');
        } else {
            $totalAmount = $order;
            $order       = Order::create();
        }
        $data = [
            'METHOD'   => 'DoExpressCheckoutPayment',
            'VERSION'  => self::API_VERSION,
            'TOKEN'    => $token,
            'PAYERID'  => $payer_id,
            'MSGSUBID' => $payer_id,

            'USER'      => $api_user,
            'PWD'       => $api_pwd,
            'SIGNATURE' => $api_signature,

            'PAYMENTREQUEST_0_AMT'          => (float)number_format($totalAmount, 2, '.', ''),
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
        ];

        $data = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Paypal.processPaymentData', $data, [
            'orderId' => $order->getId(),
        ]));

        $sdata     = html_entity_decode(urldecode(http_build_query($data)));
        $Connector = new WSConnector($url);
        $Connector->setRespFormat('text/html');
        $response = $Connector->request('', $sdata, 'post', 'Paypal.processPayment');

        parse_str(urldecode($response['response']), $__response);

        $logMsg = "
            {$__response['L_LONGMESSAGE0']}
            Data: " . print_r($data, true) . "
            Response: " . print_r($__response, true) . "
        ";

        if ($__response['ACK'] != 'Success') {
            if ($__response['L_ERRORCODE0'] == 10486) {
                Utils::log('Paypal.processPayment.response4', $logMsg, 'WARNING', true);

                header('Location: ' . $url . '?' . $sdata);
                exit;
            } else {
                Utils::log('Paypal.processPayment.response3', $logMsg, 'ERROR', true);
                Session::clearCheckout();
                throw new PaypalException($__response['L_LONGMESSAGE0'], $__response['L_ERRORCODE0']);
            }
        }
        if ($__response['PAYMENTINFO_0_PAYMENTSTATUS'] != 'Completed' && $__response['PAYMENTINFO_0_PAYMENTSTATUS'] != 'Pending' && $__response['PAYMENTINFO_0_PAYMENTSTATUS'] != 'Completed_Funds_Held') {
            $logMsg = "
                The Payment with Transaction-ID = {$__response['PAYMENTINFO_0_TRANSACTIONID']} " . "has status = '{$__response['PAYMENTINFO_0_PAYMENTSTATUS']}'
            " . $logMsg;
            Utils::log('Paypal.processPayment.response2', $logMsg, 'ERROR', true);
            throw new PaypalException($__response['L_LONGMESSAGE0'], $__response['L_ERRORCODE0']);
        } else {
            // log successful payment
            Utils::log('Paypal.processPayment.response1', $logMsg, 'INFO');
            if ($__response['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Completed') {
                // update status
                $order->setValue('status', 'IP');
            }
        }

        $this->responses['processPayment'] = $__response;
        $order->setValue('payment', $this);
        Session::setCheckoutData('Order', $order);
        $order->save();
        return strtolower($__response['PAYMENTINFO_0_PAYMENTSTATUS']);
    }

    public function processAsyncIPN($order)
    {
        /*
         * IPN Simulator
         * https://developer.paypal.com/developer/ipnSimulator/
         *
         * um diese Funktion zu testen muss man im Simulator
         * folgendes eingeben:
         * IPN handler URL: <ipn_notification_url-welche-oben-angegeben-ist>
         * Transaction Type: Express Checkout
         * payment_status: Completed
         * txn_id: <PAYMENTINFO_0_TRANSACTIONID--aus-der-Bestellungs-Response-von-paypal-entnehmen>
         */
        $Settings   = \rex::getConfig('simpleshop.PaypalExpress.Settings');
        $prefix     = from_array($Settings, 'api_type', '');
        $status     = rex_post('payment_status', 'string');
        $useSandbox = $prefix == 'sandbox_';
        $verified   = false;

        $ipn = new \PaypalIPN();
        $ipn->usePHPCerts();

        if ($useSandbox) {
            $ipn->useSandbox();
        }
        try {
            $verified = $ipn->verifyIPN();
        } catch (\Exception $ex) {
            $logMsg = "
                {$ex->getMessage()}
                Response: " . print_r($_POST, true) . "
            ";
            Utils::log('Paypal.processAsyncIPN.response2' . ($_POST["test_ipn"] == 1 ? ' [Sandbox]' : ''), $logMsg, 'ERROR', true);
        }

        if ($verified) {
            if ($status == 'Completed') {
                $logMsg = "
                    IPN Success
                    Response: " . print_r($_POST, true) . "
                ";
                Utils::log('Paypal.processAsyncIPN.response1' . ($_POST["test_ipn"] == 1 ? ' [Sandbox]' : ''), $logMsg, 'INFO');
                $this->responses['processAsyncIPN'] = $_POST;
                // update status
                $order->setValue('payment', $this);
                $order->setValue('status', 'IP');
                Session::setCheckoutData('Order', $order);
                $order->save();

                \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Payment.asyncPayment', $order, [
                    'payment' => $this,
                ]));

                CheckoutController::completeAsyncPayment($order);
            } else {
                $logMsg = "
                    IPN Status was '{$status}'
                    Response: " . print_r($_POST, true) . "
                ";
                Utils::log('Paypal.processAsyncIPN.response3' . ($_POST["test_ipn"] == 1 ? ' [Sandbox]' : ''), $logMsg, 'ERROR', true);
            }
        }

        \rex_response::cleanOutputBuffers();

        // respond to paypal
        if ($verified) {
            // Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
            \rex_response::setStatus(\rex_response::HTTP_OK);
        } else {
            \rex_response::setStatus(\rex_response::HTTP_NOT_FOUND);
            echo 'error';
        }
        exit;
    }
}

class PaypalException extends \Exception
{
}