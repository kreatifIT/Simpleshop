<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 10.10.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

$self      = $this->getVar('self');
$payment   = $this->getVar('payment');
$is_active = is_object($payment) && $payment->getPluginName() == $self->getPluginName();

try {
    /** @var Klarna $self */
    $response = $self->createSession();
} catch (KlarnaException $ex) {
    if ($ex->getCode() == 100) {
        try {
            /** @var Klarna $self */
            $response = $self->createSession();
        } catch (\Throwable $ex) {
            $errors[] = $ex->getMessage();
            Utils::log('Klarna.createSession', $ex->getMessage(), 'Error');
        }
    } else {
        $errors[] = $ex->getMessage();
        Utils::log('Klarna.createSession', $ex->getMessage(), 'Error');
    }
} catch (\Throwable $ex) {
    $errors[] = $ex->getMessage();
    Utils::log('Klarna.createSession', $ex->getMessage(), 'Error');
}

?>

    <?php if ($response && count($response['payment_method_categories'])): ?>

    <?php foreach ($response['payment_method_categories'] as $index => $paymentMethodCategory): ?>
        <div class="cell large-6 xlarge-4">
            <?php
            $isSelected = $is_active && $payment->getValue('extension') == $paymentMethodCategory['identifier'];

            $script[] = "
            Klarna.Payments.load({
                container: '#klarna-payments-container-{$paymentMethodCategory['identifier']}',
                payment_method_category: '{$paymentMethodCategory['identifier']}'
            }, function (res) {
                klarnaLoaded = " . (int)(($index - 1) == count($response['payment_method_categories'])) . ";
            });
        ";
            ?>

            <div class="klarna-panel checkout-radio-panel <?= $isSelected ? 'selected' : '' ?>" onclick="klarnaPaymentChoosen(this)">
                <div class="custom-radio">
                    <label>
                        <img src="<?= $paymentMethodCategory['asset_urls']['descriptive'] ?>">

                        <input type="radio" name="payment" value="<?= $self->getPluginName() ?>.<?= $paymentMethodCategory['identifier'] ?>" <?= $isSelected ? 'checked="checked"' : '' ?>/>
                        <span class="radio"></span>

                        <div id="klarna-payments-container-<?= $paymentMethodCategory['identifier'] ?>"></div>
                    </label>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        var klarnaLoaded = false;
        window.klarnaAsyncCallback = function () {
            Klarna.Payments.init({
                client_token: '<?= $response['client_token'] ?>'
            });
            <?= implode("\n", $script) ?>
        };

        function klarnaPaymentChoosen(_this) {
            var $input = $(_this).find('input[name=payment]'),
                value = $input.val(),
                extension = value.split('.')[1];

            Klarna.Payments.authorize({
                    payment_method_category: extension,
                    auto_finalize: true
                },
                function (res) {
                    if (res.approved) {
                        $('input[name=payment_auth_response]').val(JSON.stringify(res));
                    }
                }
            )
        }

        var klarnaHandler = window.setInterval(function () {
            if (klarnaLoaded) {
                window.clearInterval(klarnaHandler);
                jQuery('input[name=payment]:checked').trigger('click');
            }
        }, 300);
    </script>
    <script src="https://x.klarnacdn.net/kp/lib/v1/api.js" async></script>

    <input type="hidden" name="payment_auth_response" value="">

<?php endif; ?>