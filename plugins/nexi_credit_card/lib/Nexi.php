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


class Nexi extends PaymentAbstract
{
    const TEST_ID          = 89027777;
    const NAME             = 'label.nexi_credit_card';
    const SANDBOX_BASE_URL = 'https://ipg-test4.constriv.com';
    const LIVE_BASE_URL    = 'https://ipg.constriv.com';

    protected $responses  = [];
    protected $lang_codes = [
        'it' => 'ITA',
        'en' => 'USA',
        'fr' => 'FRA',
        'de' => 'DEU',
        'es' => 'ESP',
        'sl' => 'SLO',
        'sr' => 'SRB',
        'po' => 'POR',
        'ru' => 'RUS',
    ];


    public function getName()
    {
        if ($this->name == '') {
            $this->name = Wildcard::get(self::NAME);
        }
        return parent::getName();
    }

    protected function getLangCode($lang_code = null)
    {
        if (!$lang_code) {
            $lang_code = \rex_clang::getCurrent()
                ->getCode();
        }
        return $this->lang_codes[$lang_code] ?: $this->lang_codes['en'];
    }

    public function initPayment($order_id, $total_amount, $order_descr)
    {
        $Settings       = \rex::getConfig('simpleshop.Nexi.Settings');
        $test_mode      = from_array($Settings, 'use_test_mode', false);
        $url            = $test_mode ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
        $tran_portal_id = $test_mode ? $Settings['tran_portal_test_id'] : $Settings['tran_portal_id'];
        $password       = $test_mode ? $Settings['test_password'] : $Settings['password'];

        if ($tran_portal_id == '' || $password == '') {
            throw new NexiException('The Nexi Credentials are not set!', 1);
        }

        $data = [
            'id'           => $tran_portal_id,
            'password'     => $password,
            'action'       => 1, // Transaction-type = Acquisto (“Purchase”)
            'currencycode' => 978, // ISO-Code for EUR
            'amt'          => (float)number_format($total_amount, 2), // total payment (including tax + shipping)
            'langid'       => $this->getLangCode(),
            'responseURL'  => html_entity_decode(rex_getUrl(null, null, ['action' => 'process_ipn', 'order_id' => $order_id])),
            'errorURL'     => html_entity_decode(rex_getUrl(null, null, ['action' => 'cancelled', 'ts' => time()])),
            'trackid'      => $order_id,
            'udf1'         => sha1('order' . $order_id . time()),
            'udf2'         => $order_descr,
        ];

        $data = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Nexi.initPaymentData', $data));

        $sdata     = htmlspecialchars_decode(http_build_query($data));
        $Connector = new WSConnector($url);
        $Connector->setReqFormat(null);
        $Connector->setRespFormat('application/x-www-form-urlencoded');
        $response = $Connector->request('/IPGWeb/servlet/PaymentInitHTTPServlet', $sdata, 'post', 'Nexi.initPayment');

        if ($response['response'] == '') {
            $log_msg = "
                Response was empty
                Data: " . print_r($data, true) . "
                Response: " . print_r($response, true) . "
            ";
            Utils::log('Nexi.initPayment.response', $log_msg, 'ERROR', true);
            throw new NexiException('Response was empty', 20);
        } else if (substr($response['response'], 0, 7) == '!ERROR!') {
            $log_msg = "
                {$response['response']}
                Data: " . print_r($data, true) . "
                Response: " . print_r($response, true) . "
            ";
            Utils::log('Nexi.initPayment.response', $log_msg, 'ERROR', true);
            throw new NexiException($response['response'], 21);
        } else {
            $delimiter_pos            = strpos($response['response'], ':http');
            $response['payment_id']   = substr($response['response'], 0, $delimiter_pos);
            $response['payment_url']  = substr($response['response'], $delimiter_pos + 1);
            $response['redirect_url'] = "{$response['payment_url']}?PaymentID={$response['payment_id']}";

            $log_msg = "
                {$response['response']}
                TransactionId: {$response['payment_id']}
                Data: " . print_r($data, true) . "
                Response: " . print_r($response, true) . "
            ";
            Utils::log('Nexi.initPayment.response', $log_msg, 'INFO');

            // save transaction id
            Session::setCheckoutData('nexi_transaction_id', $response['payment_id']);
            Session::setCheckoutData('nexi_nonce', $data['udf1']);

            $this->responses['initPayment'] = $response;

            $Order = Session::getCurrentOrder();
            $Order->setValue('payment', $this);
            Session::setCheckoutData('Order', $Order);
            $Order->save();
        }

        return $response['redirect_url'];
    }

    public function processIPN($Order, $post_data)
    {
        $responses = $this->getValue('responses', []);

        if (isset($responses['initPayment']['payment_id']) && $responses['initPayment']['payment_id'] == $post_data['paymentid']) {
            $responses['processIPN'] = $post_data;
            $this->setValue('responses', $responses);

            $Order->setValue('payment', $this);
            Session::setCheckoutData('Order', $Order);
            $Order->save();

            if ($post_data['result'] == 'APPROVED' || $post_data['result'] == 'CAPTURED') {
                Utils::log('Nexi.processIPN', 'successfull', 'INFO');

                \rex_response::cleanOutputBuffers();
                echo 'REDIRECT=' . html_entity_decode(rex_getUrl(null, NULL, ['action' => 'complete', 'ts' => time()]));
            } else {
                throw new NexiException("Nexi.processIPN.result - Payment transaction failed [result = {$post_data['result']}]", 30);
            }
        } else {
            throw new NexiException("Nexi.processIPN - POST-PaymentID [{$post_data['paymentid']}] did not correspond to Payment.payment_id [{$responses['initPayment']['payment_id']}]", 31);
        }
    }
}

class NexiException extends \Exception
{
}