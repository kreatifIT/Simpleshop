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

    protected function _execute()
    {
        $this->params = array_merge([
            'show_steps_fragment' => true,
            'needs_login'         => true,
            'Customer'            => Customer::getCurrentUser(),
        ], $this->params);

        $this->Order = Session::getCurrentOrder();

        $this->setVar('Order', $this->Order);
        $this->verifyParams(['action']);

        if ($this->params['needs_login'] && !Customer::isLoggedIn()) {
            $this->fragment_path[] = 'simpleshop/customer/auth/login.php';
            return $this;
        }


        switch ($this->params['action']) {
            default:
                $this->products = Session::getCartItems(true);

                if (count($this->products)) {
                    $currentStep = $this->getCurrentStep();
                    $doneSteps   = $this->getDoneSteps();
                    $backStep    = $doneSteps[array_search($currentStep, $doneSteps) - 1];

                    if (strlen($backStep)) {
                        $back_url = rex_getUrl(null, null, ['step' => $backStep]);
                    } else {
                        $Settings = \rex::getConfig('simpleshop.Settings');
                        $back_url = rex_getUrl($Settings['linklist']['cart']);
                    }

                    $this->setVar('current_step', $currentStep);
                    $this->setVar('back_url', $back_url);

                    if ($this->params['show_steps_fragment']) {
                        $this->fragment_path[] = 'simpleshop/checkout/steps.php';
                    }

                    switch ($currentStep) {
                        case 'invoice_address':
                            return $this->getInvoiceAddressView();
                        case 'shipping_address':
                            return $this->getShippingAddressView();
                        case 'shipping':
                        case 'payment':
                        case 'shipping||payment':
                            return $this->getShippingPaymentView();
                        case 'show-summary':
                            return $this->getSummaryView();
                    }
                } else {
                    // no products - redirect to shopping cart
                    \rex_response::sendCacheControl();
                    \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
                    rex_redirect($this->settings['linklist']['cart'], null, ['ts' => time()]);
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
        $i           = 0;
        $currentStep = self::getCurrentStep();
        $steps       = FragmentConfig::getValue('checkout.steps');
        $key         = current($steps);

        while ($key != $currentStep && $i < count($steps)) {
            $key = next($steps);
            $i++;
        }
        return next($steps);
    }

    public static function setDoneStep($step)
    {
        $doneSteps = Session::getCheckoutData('steps_done', []);

        if (!in_array($step, $doneSteps)) {
            $doneSteps[] = $step;
        }
        Session::setCheckoutData('steps_done', $doneSteps);
    }

    public static function processIPN()
    {
        $order_id        = rex_get('order_id', 'int');
        $data['SERVER']  = $_SERVER;
        $data['POST']    = $_POST;
        $data['Order']   = $order_id ? Order::get($order_id) : null;
        $data['Payment'] = $data['Order'] ? $data['Order']->getValue('payment') : null;

        \rex_file::put(\rex_path::addonData('simpleshop', 'ipn/' . date('Ymd-His') . '.log'), print_r($data, true));

        if ($data['Payment']) {
            try {
                $data['Payment']->processIPN($data['Order'], $_POST);
                $data['Order']->setValue('status', 'IP');
                $data['Order']->setValue('payment', Order::prepareData($data['Payment']));
                Session::setCheckoutData('Order', $data['Order']);
                $data['Order']->save();
            } catch (\Exception $ex) {
                Utils::log('Checkout.processIPN', $ex->getMessage() . "\n" . print_r($data, true), 'ERROR', true);
            }
        }
        exit;
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

        \rex_response::sendCacheControl();
        \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
        rex_redirect(null, null, ['step' => 'show-summary', 'ca-info' => 1, 'ts' => time()]);
    }

    protected function getShippingAddressView()
    {
        $Address      = $this->Order->getValue('shipping_address');
        $useShAddress = Session::getCheckoutData('use_shipping_address', false);
        $Order        = Session::getCurrentOrder();
        $customer_id  = $this->params['Customer']->getId();

        $stmt = CustomerAddress::query();
        $stmt->where('status', 1);
        $stmt->where('customer_id', $customer_id);
        $stmt->where('id', $this->params['Customer']->getValue('invoice_address_id'), '!=');
        $stmt->orderBy('id', 'Desc');
        $addresses = $stmt->find();

        if (!$Address) {
            if (isset($this->params['Address'])) {
                $Address = $this->params['Address'];
            } else {
                $Address = $Order->getShippingAddress();
            }
        }

        if (!empty($_POST)) {
            $goAhead        = false;
            $shippingIsIdem = rex_post('shipping_address_is_idem', 'int', 0);
            $addressId      = rex_post('shipping-address', 'int', 0);
            $address        = $addressId ? CustomerAddress::get($addressId) : null;

            Session::setCheckoutData('use_shipping_address', !$shippingIsIdem);
            $Order->setValue('shipping_address', null);
            $Order->setValue('shipping_address_id', null);

            if ($shippingIsIdem) {
                $goAhead = true;
            } else if ($address) {
                $goAhead = true;
                $Order->setValue('shipping_address', $address);
                $Order->setValue('shipping_address_id', $addressId);
            }

            Session::setCheckoutData('Order', $Order);

            if ($goAhead) {
                $nextStep = CheckoutController::getNextStep();
                CheckoutController::setDoneStep($nextStep);
                \rex_response::sendCacheControl();
                \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
                rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep, 'ts' => time()]));
            }
        }

        $excludedFields = FragmentConfig::$data['yform_fields']['rex_shop_customer_address']['_excludedFields'];
        $excludedFields = array_merge($excludedFields, FragmentConfig::$data['checkout']['shipping_excl_fields']);

        $this->setVar('use_shipping_address', $useShAddress);
        $this->setVar('excluded_fields', $excludedFields);
        $this->setVar('addresses', $addresses);
        $this->setVar('Address', $Address ?: CustomerAddress::create());

        \rex_extension::register('YFORM_DATA_ADDED', ['\FriendsOfREDAXO\Simpleshop\CheckoutController', 'shippingAddressCallback']);
        \rex_extension::register('YFORM_DATA_UPDATED', ['\FriendsOfREDAXO\Simpleshop\CheckoutController', 'shippingAddressCallback']);
        $this->fragment_path[] = 'simpleshop/checkout/customer/shipping_address.php';
    }

    public static function shippingAddressCallback(\rex_extension_point $Ep)
    {
        $yform = $Ep->getSubject();
        $table = $Ep->getParam('table')
            ->getTableName();

        if (!\rex::isBackend() && $table == CustomerAddress::TABLE && $yform->isSend() && !$yform->hasWarnings()) {
            $Object   = $Ep->getParam('data');
            $nextStep = CheckoutController::getNextStep();
            $Order    = Session::getCurrentOrder();

            // NEEDED! to get data
            $Object->getValue('createdate');
            $Order->setValue('shipping_address', $Object);
            $Order->setValue('shipping_address_id', $Object->getId());
            Session::setCheckoutData('Order', $Order);

            CheckoutController::setDoneStep($nextStep);
            \rex_response::sendCacheControl();
            \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
            rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep, 'ts' => time()]));
        }
        return $yform;
    }

    protected function getInvoiceAddressView()
    {
        $Address     = $this->Order->getInvoiceAddress();
        $Address     = $Address ? CustomerAddress::get($Address->getId()) : null;
        $customer_id = $this->params['Customer']->getId();

        if (!$Address && $customer_id > 0) {
            if (isset($this->params['Address'])) {
                $Address = $this->params['Address'];
            } else if ($this->params['Customer']->getvalue('invoice_address_id') > 0) {
                $Address = CustomerAddress::get($this->params['Customer']->getvalue('invoice_address_id'));
            } else {
                $stmt = CustomerAddress::query();
                $stmt->where('status', 1);
                $stmt->orderBy('id', 'desc');
                $stmt->where('customer_id', $customer_id);
                $Address = $stmt->findOne();
            }
        }

        $excludedFields = FragmentConfig::$data['yform_fields']['rex_shop_customer_address']['_excludedFields'];
        $excludedFields = array_merge($excludedFields, FragmentConfig::$data['checkout']['invoice_excl_fields']);

        $this->setVar('excluded_fields', $excludedFields);
        $this->setVar('Address', $Address ?: CustomerAddress::create());

        \rex_extension::register('YFORM_DATA_ADDED', ['\FriendsOfREDAXO\Simpleshop\CheckoutController', 'invoiceAddressCallback']);
        \rex_extension::register('YFORM_DATA_UPDATED', ['\FriendsOfREDAXO\Simpleshop\CheckoutController', 'invoiceAddressCallback']);
        $this->fragment_path[] = 'simpleshop/checkout/customer/invoice_address.php';
    }

    public static function invoiceAddressCallback(\rex_extension_point $Ep)
    {
        $yform = $Ep->getSubject();
        $table = $Ep->getParam('table')
            ->getTableName();

        if (!\rex::isBackend() && $table == CustomerAddress::TABLE && $yform->isSend() && !$yform->hasWarnings()) {

            $Object   = $Ep->getParam('data');
            $Order    = Session::getCurrentOrder();
            $nextStep = CheckoutController::getNextStep();

            // NEEDED! to get data
            $Object->getValue('createdate');
            $Order->setValue('invoice_address', $Object);
            $Order->setValue('customer_id', $Object->getValue('customer_id'));
            Session::setCheckoutData('Order', $Order);

            CheckoutController::setDoneStep($nextStep);
            \rex_response::sendCacheControl();
            \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
            rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep, 'ts' => time()]));
        }
        return $yform;
    }

    protected function getShippingPaymentView()
    {
        $shippings  = [];
        $_shippings = Shipping::getAll();
        $payments   = Payment::getAll();

        foreach ($_shippings as $shipping) {
            if ($shipping->usedForFrontend()) {
                $shippings[] = $shipping;
            }
        }

        if (empty($shippings) && empty($payments)) {
            $nextStep = CheckoutController::getNextStep();
            CheckoutController::setDoneStep($nextStep);
            \rex_response::sendCacheControl();
            \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
            rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep, 'ts' => time()]));
        } else if (rex_post('action', 'string') == 'set-shipping-payment') {
            try {
                $this->Order->setValue('shipping', Order::prepareData(Shipping::get(rex_post('shipment', 'string'))));
            } catch (RuntimeException $ex) {
            }

            try {
                $this->Order->setValue('payment', Order::prepareData(Payment::get(rex_post('payment', 'string'))));
            } catch (RuntimeException $ex) {
            }

            Session::setCheckoutData('Order', $this->Order);

            $nextStep = CheckoutController::getNextStep();
            CheckoutController::setDoneStep($nextStep);
            \rex_response::sendCacheControl();
            \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
            rex_redirect(null, null, array_merge($_GET, ['step' => $nextStep, 'ts' => time()]));
        }

        $this->setVar('currentStep', $this->getCurrentStep());
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
                } catch (CouponException $ex) {
                    $warnings[] = ['label' => $ex->getLabelByCode()];
                }
                break;

            case 'place_order':
                $tos_accepted  = rex_post('tos_accepted', 'int');
                $rma_accepted  = rex_post('rma_accepted', 'int');
                $remarks       = trim(rex_post('remarks', 'string'));
                $minOrderValue = CartController::getMinOrderValue();
                $this->Order->setValue('remarks', $remarks);

                if (array_sum($this->Order->getValue('brut_prices')) < $minOrderValue) {
                    $warnings[] = ['label' => strtr(Wildcard::get('error.min_order_value'), ['{VALUE}' => '<strong>' . format_price($minOrderValue) . ' &euro;</strong>'])];
                } else if ($tos_accepted && $rma_accepted) {
                    try {
                        $Payment = $this->Order->getValue('payment');

                        if (!$Payment) {
                            $payments = Payment::getAll();

                            if (count($payments)) {
                                $this->Order->setValue('payment', current($payments));
                            } else {
                                throw new OrderException('No payment gateway available');
                            }
                        }
                        $this->Order->setValue('status', 'OP');

                        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Checkout.beforePlaceOrder', $this->Order, []));

                        $this->Order->save(false);
                        \rex_response::sendCacheControl();
                        \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
                        rex_redirect(null, null, ['action' => 'init-payment', 'ts' => time()]);
                    } catch (OrderException $ex) {
                        $warnings[] = ['label' => $ex->getMessage()];
                    }
                } else {
                    $warnings[] = ['label' => '###error.tos_rma_not_accepted###'];
                }
                break;
            default:
                $warnings = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Checkout.summaryDefaultAction', $warnings, [
                    "Order"      => $this->Order,
                    "postAction" => $postAction,
                ]));

                break;
        }

        $coupon_code = Session::getCheckoutData('coupon_code');

        if ($coupon_code != '') {
            try {
                Coupon::redeem($coupon_code);
            } catch (CouponException $ex) {
                $warnings[] = ['label' => $ex->getLabelByCode()];
            }
        }

        try {
            $warnings = array_merge($warnings, $this->Order->calculateDocument($this));
        } catch (OrderException $ex) {
            $errors[] = $ex->getMessage();
        }

        // verify product existance
        $product_cnt = count(Session::getCartItems(true));

        if ($product_cnt < 1) {
            $errors[] = Wildcard::get('shop.error_summary_no_product_available');
        }

        if ($this->Order->getValue('status') && rex_get('ca-info', 'int') == 1) {
            $warnings[] = ['label' => '###label.payment_cancelled###'];
        }
        Session::setCheckoutData('Order', $this->Order);

        $this->fragment_path[] = 'simpleshop/checkout/summary/wrapper.php';
        $this->setVar('errors', $errors);
        $this->setVar('warnings', $warnings, false);
        $this->setVar('coupon_code', $coupon_code);
        $this->setVar('products', $this->Order->getValue('products'));
        $this->setVar('Config', FragmentConfig::getValue('checkout'));
        $this->setVar('cart_url', rex_getUrl($this->settings['linklist']['cart']));
    }

    public function sendMail($debug = false)
    {
        $do_send  = true;
        $Mail     = new Mail();
        $Settings = \rex::getConfig('simpleshop.Settings');
        $Customer = $this->Order->getCustomerData();

        $Mail->Subject = '###label.email__order_complete###';
        $Mail->setFragmentPath('simpleshop/email/order/complete.php');

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

        $type = \FriendsOfREDAXO\Simpleshop\Utils::getSetting('use_invoicing', false) && $this->Order->getInvoiceNum() ? 'invoice' : 'order';

        if (FragmentConfig::$data['checkout']['generate_pdf']) {
            $PDF = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Checkout.setInvoicePDF', null, [
                'type'  => $type,
                'User'  => $Customer,
                'Order' => $this->Order,
            ]));
            $PDF = $this->Order->getInvoicePDF($type, $debug === 1, $PDF);
            $PDF = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Checkout.getInvoicePDF', $PDF, [
                'type'  => $type,
                'User'  => $Customer,
                'Order' => $this->Order,
            ]));

            if ($debug === 2) {
                $PDF->Output();
                exit;
            }
            $Mail->addStringAttachment($PDF->Output('', 'S'), \rex::getServerName() . ' - ' . Wildcard::get('label.' . $type) . '.pdf', 'base64', 'application/pdf');
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

    public static function completeAsyncPayment($order)
    {
        $_this = new self();
        try {
            $_this->Order = $order;
            $_this->Order->completeOrder();
            $_this->sendMail();
        } catch (OrderException $ex) {
            $logMsg = "
                {$ex->getMessage()}
                Uri: " . rex_server('REQUEST_URI', 'string') . " 
                GET: " . print_r($_GET, true) . "
                POST: " . print_r($_POST, true) . "
            ";
            Utils::log('Checkout.completeAsyncPayment', $logMsg, 'ERROR', true);
        }
    }

    protected function getCompleteView()
    {
        $status = rex_get('status', 'string', 'completed');

        if (!$this->Order->getId()) {
            \rex_response::sendCacheControl();
            \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
            rex_redirect($this->settings['linklist']['cart'], null, ['ts' => time()]);
        }
        try {
            if ($status == 'completed') {
                // finally save order - DONE / COMPLETE
                $this->Order->completeOrder();
                $this->sendMail($this->params['debug']);
            }
            \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Checkout.getCompleteView', $this, [
                'status' => $status,
            ]));
        } catch (OrderException $ex) {
            echo '<div class="row column"><div class="margin callout alert">' . $ex->getMessage() . '</div></div>';
            return;
        }

        // CLEAR THE SESSION
        Session::clearCheckout();
        Session::clearCart();

        switch ($status) {
            case 'completed':
                $this->fragment_path[] = 'simpleshop/checkout/complete.php';
                break;

            default:
            case 'pending':
            case 'completed_funds_held':
                $this->fragment_path[] = 'simpleshop/checkout/pending.php';
                break;
        }
    }

    protected function initPayment()
    {
        $payment               = $this->Order->getValue('payment');
        $this->fragment_path[] = 'simpleshop/checkout/payment/' . $payment->getValue('plugin_name') . '/payment_init.php';
    }

    protected function doPay()
    {
        $order   = Order::findByPaymentToken();
        $payment = $order ? $order->getValue('payment') : null;

        if ($payment) {
            $this->setVar('Order', $order);
            $this->fragment_path[] = 'simpleshop/checkout/payment/' . $payment->getValue('plugin_name') . '/payment_process.php';
        } else {
            $logMsg = "
                Invalid Checkout request
                Uri: " . rex_server('REQUEST_URI', 'string') . " 
                GET: " . print_r($_GET, true) . "
                POST: " . print_r($_POST, true) . "
            ";
            Utils::log('Checkout.doPay', $logMsg, 'ERROR', true);

            echo '<div class="grid-container margin-top margin-bottom"><div class="cell"><div class="callout alert">Request not valid</div></div></div>';
        }
    }
}