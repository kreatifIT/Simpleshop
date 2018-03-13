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

use FriendsOfREDAXO\Simpleshop;

if (rex::isBackend())
{
    echo '<h3>Checkout-Prozess</h3>';
    return;
}

$errors        = [];
$CHECKOUT      = Simpleshop\Session::getCheckoutData();
$Order         = Simpleshop\Session::getCurrentOrder();
$fragment      = new rex_fragment();
$fragment_path = NULL;
$action        = rex_request('action', 'string');
$step          = rex_request('step', 'int');
$products      = Simpleshop\Session::getCartItems(TRUE);
$has_shipping  = Simpleshop\Session::getCheckoutData('has_shipping');

$fragment->setVar('has_shipping', $has_shipping);

if (empty ($products))
{
    // no products - redirect to shopping cart
    rex_redirect(\Kreatif\Project\Settings::CART_PAGE_ID);
}
else if ($CHECKOUT['step'] > 2 && !$Order)
{
    $CHECKOUT['step'] = 2;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ADDRESS FORM
if ($action == 'guest-checkout' || ($CHECKOUT['step'] >= 2 && $step == 2) || $CHECKOUT['step'] == 2)
{
    // show form
    Simpleshop\Session::setCheckoutData('step', 2);
    Simpleshop\Session::setCheckoutData('current_step', 2);
    Simpleshop\Session::setCheckoutData('as_guest', strlen($action) ? $action == 'guest-checkout' : $CHECKOUT['as_guest']);

    // register saving callback
    rex_extension::register('simpleshop.CustomerAddress.addresses_saved', function ($params)
    {
        Simpleshop\Session::setCheckoutData('step', 3);

        $Order     = Simpleshop\Session::getCurrentOrder();
        $addresses = $params->getSubject();
        $user_id   = (int) $params->getParam('user_id');


        foreach ($addresses as $index => $address)
        {
            $Order->setValue('address_' . $index, $address);
        }
        $Order->setValue('extras', ['address_extras' => $params->getParam('extras')]);
        $Order->setValue('customer_id', $user_id);
        header('Location: ' . rex_getUrl());
        exit();
    });

    $fragment_path = 'simpleshop/checkout/customer/address_form.php';
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// SHIPPING AND PAYMENT SELECTION
else if (($CHECKOUT['step'] >= 3 && $step == 3) || $CHECKOUT['step'] == 3)
{
    Simpleshop\Session::setCheckoutData('current_step', 3);

    // show form
    if ($action == 'process_shipping_and_payment')
    {
        $shipment = rex_post('shipment', 'string');
        $payment  = rex_post('payment', 'string');

        if (($has_shipping && !strlen($shipment)) || !strlen($payment))
        {
            $errors[] = '###error.payment_shipping_not_selected###';
        }
        else
        {
            if ($has_shipping)
            {
                $Order->setValue('shipping', \FriendsOfREDAXO\Simpleshop\Shipping::get($shipment));
            }
            $Order->setValue('payment', \FriendsOfREDAXO\Simpleshop\Payment::get($payment));
            Simpleshop\Session::setCheckoutData('step', 4);
            header('Location: ' . rex_getUrl());
            exit();
        }
    }
    $fragment_path = 'simpleshop/checkout/shipping_and_payment/wrapper.php';
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PAYMENT STEP - INIT AND REDIRECT TO EXTERNAL SERVICE IF NEEDED
else if ($CHECKOUT['step'] >= 4 && $action == 'pay')
{
    // save order
    $total = $Order->getValue('total');

    if ($total > 0)
    {
        $Order->setValue('status', 'OP');
        $Order->save();

        $payment = $Order->getValue('payment');
        $fragment->setVar('order', $Order);
        $fragment_path = 'simpleshop/payment/' . $payment->plugin_name . '/payment_init.php';
    }
    else
    {
        $Order->setValue('status', 'IP');
        $Order->save();

        header('Location: ' . rex_getUrl(null, null, ['action' => 'complete']));
        exit();
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PAYMENT PROCESS STEP - PROCESS PAYMENT
else if ($CHECKOUT['step'] >= 4 && $action == 'pay_process')
{
    $payment = $Order->getValue('payment');
    $fragment->setVar('order', $Order);
    $fragment_path = 'simpleshop/payment/' . $payment->plugin_name . '/payment_process.php';
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PAYMENT STEP - REDIRECT TO EXTERNAL SERVICE IF NEEDED
else if ($CHECKOUT['step'] >= 4 && $action == 'complete')
{
    $extras = $Order->getValue('extras');

    if (!$extras['address_extras']['use_shipping_address'])
    {
        $Order->setValue('address_2', $Order->getValue('address_1'));
    }
    // resave order - DONE / COMPLETE
    $Order->save(TRUE);

    $Customer      = NULL;
    $do_send       = TRUE;
    $Mail          = new Simpleshop\Mail();
    $Settings      = rex::getConfig('simpleshop.Settings');
    $Mail->Subject = '###simpleshop.email.order_complete###';
    $Mail->setFragmentPath('order/complete');
    // add vars
    $Mail->setVar('Order', $Order);
    if (Simpleshop\Customer::isLoggedIn())
    {
        $Customer = Simpleshop\Customer::getCurrentUser();
        $Mail->AddAddress($Customer->getValue('email'));
    }
    else
    {
        $extras = $Order->getValue('extras');
        $Mail->AddAddress($extras['address_extras']['email']);
    }
    // set order notification email
    $Mail->AddAddress(from_array($Settings, 'order_notification_email'));

    \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Checkout.orderComplete', $do_send, [
        'Mail'  => $Mail,
        'User'  => $Customer,
        'Order' => $Order,
    ]));

    if ($do_send)
    {
        $Mail->send();
    }
    $fragment->setVar('order', $Order);
    $fragment_path = 'simpleshop/checkout/complete.php';
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ORDER SUMMARY
else if (($CHECKOUT['step'] >= 4 && $step == 4) || $CHECKOUT['step'] == 4)
{
    Simpleshop\Session::setCheckoutData('current_step', 4);

    if ($action == 'place_order')
    {
        $tos_accepted = rex_post('tos_accepted', 'int');
        $rma_accepted = rex_post('rma_accepted', 'int');

        if (!$tos_accepted || !$rma_accepted)
        {
            $errors[] = '###error.tos_rma_not_accepted###';
        }
        else
        {
            header('Location: ' . rex_getUrl(NULL, NULL, ['action' => 'pay']));
            exit();
        }
    }
    else if ($action == 'redeem_coupon')
    {
        try
        {
            $code   = rex_post('coupon', 'string');
            $coupon = Simpleshop\Coupon::redeem($code);
            $fragment->setVar('code', $code);
            // save coupon to apply it also on page refresh
            Simpleshop\Session::setCheckoutData('coupon_code', $code);
        }
        catch (Simpleshop\CouponException $ex)
        {
            $errors[] = $ex->getLabelByCode();
        }
    }
    else if ($action == 'cancelled')
    {
        \FriendsOfREDAXO\Simpleshop\Utils::log('Payment cancelled', 'Customer cancelled payment', 'INFO');
        $Order->setValue('status', 'CA');
        $Order->save();
    }
    else
    {
        $code = Simpleshop\Session::getCheckoutData('coupon_code');
        $fragment->setVar('code', $code);
        Simpleshop\Coupon::redeem($code);
    }

    $fragment->setVar('order', $Order);
    $fragment_path = 'simpleshop/checkout/summary/wrapper.php';
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// LOGIN - GUEST CHECKOUT
else if (!\FriendsOfREDAXO\Simpleshop\Customer::isLoggedIn())
{
    // init / reset session settings
    Simpleshop\Session::setCheckoutData('step', 1);
    Simpleshop\Session::setCheckoutData('current_step', 1);

    // show login + registration + guest checkout
    $sub_fragment = new rex_fragment();
    $content      = $sub_fragment->parse('simpleshop/checkout/customer/guest_info.php');

    $fragment->setVar('class', 'row boxes-checkout');
    $fragment->setVar('sub_class', 'large-4 columns margin-bottom box-checkout');
    $fragment->setVar('after', $content, FALSE);
    $fragment_path = 'simpleshop/customer/auth/login_wrapper.php';
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// REDIRECT TO ADDRESS FORM
else
{
    // user is logged in and ready to start the checkout process
    // redirect him to the address form
    Simpleshop\Session::setCheckoutData('step', 2);
    header('Location: ' . rex_getUrl());
    exit();
}

// OUTPUT

// Steps Checkout
if ($action != 'complete')
{
    $step_fragment = new rex_fragment();
    $step_fragment->setVar('current_step', Simpleshop\Session::getCheckoutData('current_step'));
    echo $step_fragment->parse('simpleshop/checkout/steps.php');
}


if (count($errors)): ?>
    <?php foreach ($errors as $error): ?>
        <div class="row column margin-small-bottom">
            <div class="callout alert"><?= $error ?></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $fragment->parse($fragment_path);