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

$_FUNC     = rex_post('func', 'string');
$order_ids = rex_post('orders', 'array', []);

if ($_FUNC == 'submit' && count($order_ids))
{
    $order_cnt = 0;
    $error     = '';
    try
    {
        $order_cnt = Omest::sendOrdersToOLC($order_ids, TRUE);
    }
    catch (OmestShippingException $ex)
    {
        switch ($ex->getCode())
        {
            case 1:
                preg_match('!\[(\d+)\]!', $ex->getMessage(), $matches);
                $error = sprintf($this->i18n('omest_shipping.error.order_has_no_product'), $matches[1]);
                break;
            default:
                $error = $ex->getMessage();
                break;
        }
    }
    catch (WSConnectorException $ex)
    {
        switch ($ex->getCode())
        {
            case 1:
                $error = checkstr(strtr(Wildcard::get('error.ws_not_available'), ['{{service}}' => 'Omest OLC']), $ex->getMessage());
                break;
            case 2:
                $error = checkstr(strtr(Wildcard::get('error.ws_not_reachable'), ['{{service}}' => 'Omest OLC']), $ex->getMessage());
                break;
            case 3:
                $error = checkstr(strtr(Wildcard::get('error.ws_wrong_response_status'), ['{{service}}' => 'Omest OLC']), $ex->getMessage());
                break;
            default:
                $error = $ex->getMessage();
                break;
        }
    }

    if ($order_cnt <= 0)
    {
        echo \rex_view::error($this->i18n('omest_shipping.orders_not_submitted') . (strlen($error) ? ': "' . $error . '"' : ''));
    }
    else
    {
        echo \rex_view::info(sprintf($this->i18n('omest_shipping.orders_submitted'), $order_cnt));
    }
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
$fragment->setVar('order_ids', $order_ids);
$content = $fragment->parse('simpleshop/backend/omest_shipping_orders.php');

$fragment = new \rex_fragment();
$fragment->setVar('body', $content, FALSE);
$fragment->setVar('class', 'edit', FALSE);
$fragment->setVar('title', $this->i18n('omest_shipping.set_orders_to_send'), FALSE);
$sections .= $fragment->parse('core/page/section.php');

$formElements = [
    ['field' => '<button class="btn btn-apply rex-form-aligned" type="submit" name="func" value="submit"' . \rex::getAccesskey(\rex_i18n::msg('action.submit'), 'apply') . '>' . \rex_i18n::msg('action.submit') . '</button>'],
];
$fragment     = new \rex_fragment();
$fragment->setVar('elements', $formElements, FALSE);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new \rex_fragment();
$fragment->setVar('class', 'edit', FALSE);
$fragment->setVar('buttons', $buttons, FALSE);
$sections .= $fragment->parse('core/page/section.php');

echo '<form action="' . \rex_url::currentBackendPage() . '" method="post">' . $sections . '</form>';