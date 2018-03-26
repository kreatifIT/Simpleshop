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


use Kreatif\Project\Settings;
use Sprog\Wildcard;
use Symfony\Component\Yaml\Exception\RuntimeException;

class CheckoutController extends Controller
{
    protected $products = [];
    protected $Order    = null;

    public static $callbackCheck = null;

    protected function _execute()
    {
        $this->params = array_merge([
            'show_steps_fragment' => true,
        ], $this->params);

        $this->products = Session::getCartItems(true);
        $this->Order    = Session::getCurrentOrder();

        $this->setVar('Order', $this->Order);
        $this->verifyParams(['action']);

        if (!Customer::isLoggedIn()) {
            $this->fragment_path[] = 'simpleshop/customer/auth/login.php';
            return $this;
        }

        if (count($this->products)) {

            switch ($this->params['action']) {
                default:
                    $currentStep = $this->getCurrentStep();
                    $doneSteps   = $this->getDoneSteps();
                    $backStep    = $doneSteps[array_search($currentStep, $doneSteps) - 1];

                    if (strlen($backStep)) {
                        $back_url = rex_getUrl(null, null, ['step' => $backStep]);
                    }
                    else {
                        $Settings = \rex::getConfig('simpleshop.Settings');
                        $back_url = rex_getUrl($Settings['linklist']['cart']);
                    }

                    if ($this->params['show_steps_fragment']) {
                        $this->setVar('current_step', $currentStep);
                        $this->fragment_path[] = 'simpleshop/checkout/steps.php';
                    }

                    $this->setVar('back_url', $back_url);

                    switch ($currentStep) {
                        case 'invoice_address':
                            return $this->getInvoiceAddressView();
                        case 'shipping_address':
                            return $this->getShippingAddressView();
                        case 'shipping||payment':
                            return $this->getShippingPaymentView();
                        case 'show-summary':
                            return $this->getSummaryView();
                    }
                    break;

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
            rex_redirect($this->settings['linklist']['cart']);
        }
    }

    public static function getDoneSteps()
    {
        $doneSteps = Session::getCheckoutData('steps_done', []);

        if (empty($doneSteps)) {
            $steps       = FragmentConfig::getValue('checkout.steps', []);
            $step        = array_shift($steps);
            $doneSteps[] = $step;
            self::setDoneStep($step);
        }
        return $doneSteps;
    }

    public static function getCurrentStep()
    {
        $doneSteps   = self::getDoneSteps();
        $currentStep = rex_get('step', 'string', $doneSteps[0]);

        if (!in_array($currentStep, $doneSteps)) {
            $currentStep = array_pop($doneSteps);
        }
        return $currentStep;
    }

    public static function getNextStep()
    {
        $currentStep = self::getCurrentStep();
        $nextSteps   = FragmentConfig::getValue('checkout.steps');
        $index       = array_search($currentStep, FragmentConfig::getValue('checkout.steps'));
        return $nextSteps[$index + 1];
    }

    public static function setDoneStep($step)
    {
        $doneSteps = Session::getCheckoutData('steps_done', []);

        if (!in_array($step, $doneSteps)) {
            $doneSteps[] = $step;
        }
        Session::setCheckoutData('steps_done', $doneSteps);
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

        rex_redirect($this->settings['linklist']['cart'], null, $_GET);
    }

    protected function getShippingAddressView()
    {
        $Address      = $this->Order->getValue('shipping_address');
        $useShAddress = Session::getCheckoutData('use_shipping_address', false);
        $Customer     = Customer::getCurrentUser();

        if (!$Address) {
            $addresses = CustomerAddress::getAll(true, [
                'filter'  => [['customer_id', $Customer->getId()]],
                'limit'   => 1,
                'orderBy' => 'id',
                'order'   => 'desc',
            ])->toArray();
            $Address   = array_shift($addresses);
        }

        if (!empty($_POST)) {
            $notUseShAddress = rex_post('shipping_address_is_idem', 'int', 0);

            Session::setCheckoutData('use_shipping_address', !$notUseShAddress);

            if ($notUseShAddress) {
                $nextStep = CheckoutController::getNextStep();
                $Order    = Session::getCurrentOrder();

                $Order->setValue('shipping_address', null);
                Session::setCheckoutData('Order', $Order);

                CheckoutController::setDoneStep($nextStep);
                rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep]));
            }
        }

        $this->setVar('use_shipping_address', $useShAddress);
        $this->setVar('Address', $Address ?: CustomerAddress::create());

        \rex_extension::register('YFORM_DATA_ADDED', ['\FriendsOfREDAXO\Simpleshop\CheckoutController', 'shippingAddressCallback']);
        \rex_extension::register('YFORM_DATA_UPDATED', ['\FriendsOfREDAXO\Simpleshop\CheckoutController', 'shippingAddressCallback']);
        $this->fragment_path[] = 'simpleshop/checkout/customer/shipping_address.php';
    }

    public static function shippingAddressCallback(\rex_extension_point $Ep)
    {
        $yform = $Ep->getSubject();
        $table = $Ep->getParam('table')->getTableName();

        if (!\rex::isBackend() && $table == CustomerAddress::TABLE && $yform->isSend() && !$yform->hasWarnings()) {
            $Object   = $Ep->getParam('data');
            $nextStep = CheckoutController::getNextStep();
            $Order    = Session::getCurrentOrder();

            // NEEDED! to get data
            $Object->getValue('createdate');
            $Order->setValue('shipping_address', $Object);
            Session::setCheckoutData('Order', $Order);

            CheckoutController::setDoneStep($nextStep);
            rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep]));
        }
        return $yform;
    }

    protected function getInvoiceAddressView()
    {
        $Address  = $this->Order->getInvoiceAddress();
        $Customer = Customer::getCurrentUser();

        CheckoutController::$callbackCheck = ['invoice' => false, 'shipping' => false];

        if (!$Address) {
            $addresses = CustomerAddress::getAll(true, [
                'filter'  => [['customer_id', $Customer->getId()]],
                'limit'   => 1,
                'orderBy' => 'id',
                'order'   => 'asc',
            ])->toArray();
            $Address   = array_shift($addresses);
        }

        $this->setVar('Address', $Address ?: CustomerAddress::create());

        \rex_extension::register('YFORM_DATA_ADDED', ['\FriendsOfREDAXO\Simpleshop\CheckoutController', 'invoiceAddressCallback']);
        \rex_extension::register('YFORM_DATA_UPDATED', ['\FriendsOfREDAXO\Simpleshop\CheckoutController', 'invoiceAddressCallback']);
        $this->fragment_path[] = 'simpleshop/checkout/customer/invoice_address.php';
    }

    public static function invoiceAddressCallback(\rex_extension_point $Ep)
    {
        $yform = $Ep->getSubject();
        $table = $Ep->getParam('table')->getTableName();

        if (!\rex::isBackend() && $table == Customer::TABLE && $yform->isSend() && !$yform->hasWarnings()) {
            CheckoutController::$callbackCheck['invoice'] = true;

            $Object   = $Ep->getParam('data');
            $Order    = Session::getCurrentOrder();
            $nextStep = CheckoutController::getNextStep();

            // NEEDED! to get data
            $Object->getValue('createdate');
            $Order->setValue('customer_data', $Object);
            Session::setCheckoutData('Order', $Order);

            if (CheckoutController::$callbackCheck['shipping']) {
                CheckoutController::setDoneStep($nextStep);
                rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep]));
            }
        }
        else if (!\rex::isBackend() && $table == CustomerAddress::TABLE && $yform->isSend() && !$yform->hasWarnings()) {
            CheckoutController::$callbackCheck['shipping'] = true;

            $Object   = $Ep->getParam('data');
            $Order    = Session::getCurrentOrder();
            $nextStep = CheckoutController::getNextStep();

            // NEEDED! to get data
            $Object->getValue('createdate');
            $Order->setValue('invoice_address', $Object);
            Session::setCheckoutData('Order', $Order);

            if (CheckoutController::$callbackCheck['invoice']) {
                CheckoutController::setDoneStep($nextStep);
                rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep]));
            }
        }
        return $yform;
    }

    protected function getShippingPaymentView()
    {
        $shippings = Shipping::getAll();
        $payments  = Payment::getAll();

        if (empty($shippings) && empty($payments)) {
            $nextStep = CheckoutController::getNextStep();
            CheckoutController::setDoneStep($nextStep);
            rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep]));
        }
        else if (rex_post('action', 'string') == 'set-shipping-payment') {
            try {
                $this->Order->setValue('shipping', Shipping::get(rex_post('shipment', 'string')));
            }
            catch (RuntimeException $ex) {
            }

            try {
                $this->Order->setValue('payment', Payment::get(rex_post('payment', 'string')));
            }
            catch (RuntimeException $ex) {
            }

            Session::setCheckoutData('Order', $this->Order);

            $nextStep = CheckoutController::getNextStep();
            CheckoutController::setDoneStep($nextStep);
            rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep]));
        }

        $this->setVar('shippings', $shippings);
        $this->setVar('payments', $payments);
        $this->fragment_path[] = 'simpleshop/checkout/shipping_and_payment/wrapper.php';
    }

    protected function getSummaryView()
    {
        $errors     = [];
        $warnings   = [];
        $postAction = rex_post('action', 'string');

        switch ($postAction) {
            case 'redeem_coupon':
                try {
                    $code   = rex_post('coupon', 'string');
                    $coupon = Coupon::redeem($code);
                    $this->setVar('code', $code);
                    // save coupon to apply it also on page refresh
                    Session::setCheckoutData('coupon_code', $code);
                }
                catch (CouponException $ex) {
                    $warnings[] = ['label' => $ex->getLabelByCode()];
                }
                break;

            case 'place_order':
                $tos_accepted = rex_post('tos_accepted', 'int');
                $rma_accepted = rex_post('rma_accepted', 'int');

                if ($tos_accepted && $rma_accepted) {
                    rex_redirect(null, null, ['action' => 'init-payment']);
                }
                else {
                    $warnings[] = ['label' => '###simpleshop.error.tos_rma_not_accepted###'];
                }
                break;
        }

        try {
            $warnings = array_merge($warnings, $this->Order->calculateDocument($this));
        }
        catch (OrderException $ex) {
            $errors[] = $ex->getMessage();
        }

        // verify product existance
        $product_cnt = count(Session::getCartItems(true));

        if ($product_cnt < 1) {
            $errors[] = Wildcard::get('shop.error_summary_no_product_available');
        }

        $this->fragment_path[] = 'simpleshop/checkout/summary/wrapper.php';
        $this->setVar('errors', $errors);
        $this->setVar('warnings', $warnings);
        $this->setVar('cart_url', rex_getUrl($this->settings['linklist']['cart']));
    }

    public function sendMail($debug = false)
    {
        $do_send  = true;
        $Mail     = new \FriendsOfREDAXO\Simpleshop\Mail();
        $Settings = \rex::getConfig('simpleshop.Settings');
        $Customer = Customer::get($this->Order->getValue('customer_id'));

        $Mail->Subject = '###simpleshop.email.order_complete###';
        $Mail->setFragmentPath('order/complete');

        // add vars
        $Mail->setVar('Order', $this->Order);
        $Mail->setVar('primary_color', Settings::getValue('extended__mail__primary_color', 'red'));
        $Mail->setVar('config', []);

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
        //        $this->sendMail($this->params['debug']);
        $this->sendMail(true);

        // CLEAR THE SESSION
        //        Session::clearCheckout();
        //        Session::clearCart();

        $this->fragment_path[] = 'simpleshop/checkout/complete.php';
    }

    protected function initPayment()
    {
        $payment               = $this->Order->getValue('payment');
        $this->fragment_path[] = 'simpleshop/payment/' . $payment->getValue('plugin_name') . '/payment_init.php';
    }

    protected function doPay()
    {
        $payment               = $this->Order->getValue('payment');
        $this->fragment_path[] = 'simpleshop/payment/' . $payment->getValue('plugin_name') . '/payment_process.php';
    }
}