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
 * Url to Test-Area: https://ecommerce.nexi.it/area-test
 */

namespace FriendsOfREDAXO\Simpleshop;

use Kreatif\Model\Country;
use Sprog\Wildcard;


class NexiXPay extends PaymentAbstract
{
    const NAME             = 'simpleshop.nexi_xpay';
    const LIVE_BASE_URL    = 'https://ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet';
    const SANDBOX_BASE_URL = 'https://int-ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet';

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
        return $clang->getValue('clang_iso639_2');
    }

    public function initPayment($Order)
    {
        $Order     = Session::getCurrentOrder();
        $Settings  = \rex::getConfig('simpleshop.NexiXPay.Settings');
        $test_mode = from_array($Settings, 'use_test_mode', false);
        $base_url  = $test_mode ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
        $alias     = $test_mode ? $Settings['sandbox_alias'] : $Settings['alias'];
        $secret    = $test_mode ? $Settings['sandbox_secret'] : $Settings['secret'];

        if ($alias == '' || $secret == '') {
            throw new NexiXPayException('The NexiXPay Credentials are not set!', 1);
        }

        $currency        = 'EUR';
        $trans_code      = 'Order' . $Order->getId() . '-' . date('YmdHis');
        $total           = number_format(
            $Order->getValue('total'),
            2,
            '',
            ''
        ); // total payment (including tax + shipping)
        $customer        = $Order->getCustomerData();
        $shipping        = $Order->getShippingAddress();
        $invoice         = $Order->getInvoiceAddress();
        $shippingMethod  = $Order->getValue('shipping');
        $_countryId      = $shipping->getValue('country');
        $shippingCountry = $_countryId ? Country::get($_countryId) : null;
        $_countryId      = $invoice->getValue('country');
        $invoiceCountry  = $_countryId ? Country::get($_countryId) : null;

        // Calcolo MAC
        $mac  = sha1("codTrans={$trans_code}divisa={$currency}importo={$total}{$secret}");
        $data = [
            'alias'                     => $alias,
            'importo'                   => $total,
            'divisa'                    => $currency,
            'codTrans'                  => $trans_code,
            'mac'                       => $mac,
            'languageId'                => $this->getLangCode(),
            'url'                       => html_entity_decode(
                rex_getUrl(null, null, ['action' => 'pay-process', 'ts' => time()])
            ),
            'url_back'                  => html_entity_decode(
                rex_getUrl(null, null, ['action' => 'cancelled', 'ts' => time()])
            ),
            //'urlpost'     => html_entity_decode(rex_getUrl(null, null, ['action' => 'process-ipn', 'order_id' => $Order->getId()])),
            'descrizione'               => 'Order #' . $Order->getId(),
            'session_id'                => session_id(),
            // 3D Secure 2.1 options
            'Buyer_email'               => $customer->getValue('email'),
            'Dest_city'                 => $shipping->getValue('location'),
            'Dest_street'               => $shipping->getValue('street'),
            'Dest_street2'              => $shipping->getValue('street_additional'),
            'Dest_cap'                  => $shipping->getValue('postal'),
            'Dest_state'                => $shipping->getValue('state') ?? 'AN',
            'Dest_country'              => $shippingCountry ? $shippingCountry->getValue('iso3') : '',
            'Bill_city'                 => $invoice->getValue('location'),
            'Bill_street'               => $invoice->getValue('street'),
            'Bill_street2'              => $invoice->getValue('street_additional'),
            'Bill_cap'                  => $invoice->getValue('postal'),
            'Bill_state'                => $invoice->getValue('state') ?? 'AN',
            'Bill_country'              => $invoiceCountry ? $invoiceCountry->getValue('iso3') : '',
            'chAccDate'                 => date('Y-m-d', strtotime($customer->getValue('createdate'))),
            'preOrderPurchaseIndicator' => 1,
            'shipIndicator'             => 3,
        ];

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.NexiXPay.initPaymentData', $data));

        $redirect_url = $base_url . '?' . html_entity_decode(http_build_query($data));

        $data['redirect_url']           = $redirect_url;
        $this->responses['initPayment'] = $data;

        $Order->setValue('payment', $this);
        Session::setCheckoutData('Order', $Order);
        $Order->save();

        Utils::log('NexiXPay.initPayment', 'REDIRECT-URL: ' . $redirect_url, 'INFO');
        \rex_response::sendCacheControl();
        \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
        \rex_response::sendRedirect($redirect_url);
        exit;
    }

    public function processPayment($Order)
    {
        $Settings  = \rex::getConfig('simpleshop.NexiXPay.Settings');
        $test_mode = from_array($Settings, 'use_test_mode', false);
        $secret    = $test_mode ? $Settings['sandbox_secret'] : $Settings['secret'];
        $responses = $this->getValue('responses', []);
        $response  = \rex_request('esito', 'string');

        $data = [
            'codTrans=' . \rex_request('codTrans', 'string'),
            'esito=' . $response,
            'importo=' . \rex_request('importo', 'int'),
            'divisa=' . \rex_request('divisa', 'string'),
            'data=' . \rex_request('data', 'string'),
            'orario=' . \rex_request('orario', 'string'),
            'codAut=' . \rex_request('codAut', 'string'),
            $secret,
        ];

        // Calcolo MAC con i parametri di ritorno
        $mac_calculated = sha1(implode('', $data));
        $mac_sent       = \rex_request('mac', 'string');

        $responses['pay-process'] = $_REQUEST;
        $this->setValue('responses', $responses);

        $Order->setValue('payment', $this);
        Session::setCheckoutData('Order', $Order);

        // Verifico corrispondenza tra MAC calcolato e parametro mac di ritorno
        if ($mac_calculated != $mac_sent) {
            $Order->save();

            $msg = 'Nexi Mac checksum "' . $mac_sent . '" is incorrect; shoud be: ' . $mac_calculated;
            Utils::log('NexiXPay.processPayment', "{$msg}\n_REQUEST: \n" . print_r($_REQUEST, true), 'ERROR', true);

            throw new NexiXPayException($msg);
        } else {
            if ($response == 'OK') // log successful payment
            {
                Utils::log('NexiXPay.processPayment', 'successfull', 'INFO');
                $Order->setValue('status', 'IP');
                $Order->save();
            } else {
                $Order->save();

                $msg = \rex_request('messaggio', 'string');
                Utils::log('NexiXPay.processPayment', "{$msg}\n_REQUEST: \n" . print_r($_REQUEST, true), 'ERROR');

                throw new NexiXPayException($msg);
            }
        }
    }
}

class NexiXPayException extends \Exception
{
}