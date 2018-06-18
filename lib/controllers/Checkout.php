<?php

/**
 * This file is part of the FriendsOfREDAXO\Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 * Date: 23.03.17
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;


use Sprog\Wildcard;


class CheckoutController extends Controller
{
    protected $products = [];
    protected $Order    = null;

    protected function _execute()
    {
        $this->params = array_merge([
            'verifyAmount' => true,
        ], $this->params);

        $this->products = Session::getCartItems(true, true, $this->params['verifyAmount']);
        $this->Order    = Session::getCurrentOrder();

        $this->verifyParams(['cart_page_id', 'action']);
        $this->setVar('Order', $this->Order);

        if (count($this->products)) {
            switch ($this->params['action']) {
                case 'show-summary':
                    return $this->getSummaryView();

                case 'cancelled':
                    return $this->cancelPayment();

                case 'complete':
                    return $this->getCompleteView();

                case 'pay_process':
                case 'pay-process':
                    return $this->doPay();

                case 'init-payment':
                    return $this->initPayment();
            }
        }
        else {
            // no products - redirect to shopping cart
            rex_redirect($this->params['cart_page_id']);
        }
    }

    public function setOrder(Order $Order)
    {
        $this->Order = $Order;
    }

    protected function cancelPayment()
    {
        Utils::log('Payment cancelled', 'Customer cancelled payment', 'INFO');
        $this->Order->setValue('status', 'CA');
        $this->Order->save();

        rex_redirect($this->params['cart_page_id'], null, $_GET);
    }

    protected function getSummaryView()
    {
        $errors   = [];
        $warnings = [];

        try {
            $warnings = $this->Order->calculateDocument($this);
        }
        catch (OrderException $ex) {
            $errors[] = $ex->getMessage();
        }

        // verify product existance
        $product_cnt = count(Session::getCartItems(true, true, $this->params['verifyAmount']));

        if ($product_cnt < 1) {
            $errors[] = Wildcard::get('shop.error_summary_no_product_available');
        }

        $this->fragment_path = 'simpleshop/checkout/summary/wrapper.php';
        $this->setVar('errors', $errors);
        $this->setVar('warnings', $warnings);
        $this->setVar('cart_url', rex_getUrl($this->params['cart_page_id']));
    }

    public function sendMail($debug = false)
    {
        $do_send  = true;
        $Mail     = new \FriendsOfREDAXO\Simpleshop\Mail();
        $Settings = \rex::getConfig('simpleshop.Settings');
        $Customer = $this->Order->getInvoiceAddress();

        if ($this->Order->valueIsset('ref_order_id')) {
            if ($this->Order->getValue('status') != 'CN') {
                throw new OrderException('Credit Note status is not correct');
            }
            $Mail->Subject = '###shop.email.credit_note###';
            $Mail->setFragmentPath('order/credit_note');
        }
        else {
            $Mail->Subject = '###shop.email.order_complete###';
            $Mail->setFragmentPath('order/complete');
        }

        // add vars
        $Mail->setVar('Order', $this->Order);
        $Mail->setVar('primary_color', $this->params['primary_color']);
        $Mail->setVar('config', array_merge([
            'is_order_complete' => true,
            'is_email'          => true,
            'use_invoicing'     => from_array($Settings, 'use_invoicing'),
        ], $this->getVar('config', [])));

        // set order notification email
        $Mail->AddAddress($Customer->getValue('email'));
        $Mail->AddAddress(from_array($Settings, 'order_notification_email'));

        if (isset($this->params['addAddress'])) {
            foreach (explode(',', $this->params['addAddress']) as $_add) {
                $Mail->AddAddress($_add);
            }
        }
        if (isset($this->params['addCC'])) {
            foreach (explode(',', $this->params['addCC']) as $_add) {
                $Mail->addCC($_add);
            }
        }

        $type = $this->Order->getInvoiceNum() ? 'invoice' : 'order';
        $PDF  = $this->Order->getInvoicePDF($type, false);

        if ($PDF) {
            $Mail->addStringAttachment($PDF->Output('', 'S'), \rex::getServerName() . ' - ' . Wildcard::get('label.'. $type) . '.pdf', 'base64', 'application/pdf');
        }

        $do_send = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Checkout.orderComplete', $do_send, [
            'Mail'  => $Mail,
            'User'  => $Customer,
            'Order' => $this->Order,
        ]));

        if ($do_send) {
            $Mail->send($debug);
        }
    }

    protected function getCompleteView()
    {
        // finally save order - DONE / COMPLETE
        $this->Order->completeOrder();
        $this->sendMail($this->params['debug']);

        // CLEAR THE SESSION
        Session::clearCheckout();
        Session::clearCart();

        $this->fragment_path = 'simpleshop/checkout/complete.php';
    }

    protected function initPayment()
    {
        $payment             = $this->Order->getValue('payment');
        $this->fragment_path = 'simpleshop/payment/' . $payment->getValue('plugin_name') . '/payment_init.php';
    }

    protected function doPay()
    {
        $payment             = $this->Order->getValue('payment');
        $this->fragment_path = 'simpleshop/payment/' . $payment->getValue('plugin_name') . '/payment_process.php';
    }
}