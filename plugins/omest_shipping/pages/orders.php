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

echo \rex_view::title('Simpleshop');

$_FUNC = rex_post('func', 'string');

if($_FUNC == 'save')
{
    unset($_POST['func']);
    \rex::setConfig('simpleshop.Settings', $_POST);
}

$orders = Order::query()
    ->where('status', 'IP')
    ->where('shipping_key', '')
    ->where('shipping', '', '!=')
    ->orderBy('id')
    ->find();

$sections = '';
$fragment = new \rex_fragment();
$fragment->setVar('Addon', $this);
$fragment->setVar('orders', $orders);
$content  = $fragment->parse('simpleshop/backend/omest_shipping_orders.php');

$fragment = new \rex_fragment();
$fragment->setVar('body', $content, FALSE);
$fragment->setVar('class', 'edit', FALSE);
$fragment->setVar('title', $this->i18n('omest_shipping.set_orders_to_send'), FALSE);
$sections .= $fragment->parse('core/page/section.php');

$formElements = [
    ['field' => '<button class="btn btn-apply rex-form-aligned" type="submit" name="func" value="save"' . \rex::getAccesskey(\rex_i18n::msg('action.submit'), 'apply') . '>' . \rex_i18n::msg('action.submit') . '</button>'],
];
$fragment = new \rex_fragment();
$fragment->setVar('elements', $formElements, FALSE);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new \rex_fragment();
$fragment->setVar('class', 'edit', FALSE);
$fragment->setVar('buttons', $buttons, FALSE);
$sections .= $fragment->parse('core/page/section.php');

echo '<form action="' . \rex_url::currentBackendPage() . '" method="post">' . $sections . '</form>';