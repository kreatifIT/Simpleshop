<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class rex_yform_value_order_functions extends rex_yform_value_abstract
{

    public function enterObject()
    {
        if ($this->getParam('send') == 0 && $this->getParam('main_id') > 0 && rex::isBackend()) {
            $action   = rex_get('ss-action', 'string');
            $Order    = \FriendsOfREDAXO\Simpleshop\Order::get($this->getParam('main_id'));
            $Customer = $Order->getValue('customer_id') ? \FriendsOfREDAXO\Simpleshop\Customer::get($Order->getValue('customer_id')) : $Order->getInvoiceAddress();;

            // set user lang id
            if ($Customer) {
                \rex_clang::setCurrentId($Customer->getValue('lang_id', false, \rex_clang::getCurrentId()));
                setlocale(LC_ALL, \rex_clang::getCurrent()
                    ->getValue('clang_setlocale'));
            }

            switch ($action) {
                case 'generate_pdf':
                    rex_response::cleanOutputBuffers();
                    $PDF = $Order->getInvoicePDF('invoice', false);
                    $PDF->Output();
                    exit;

                case 'resend_email':
                    $Controller = new \FriendsOfREDAXO\Simpleshop\CheckoutController();
                    $Controller->setOrder($Order);
                    $Controller->sendMail();

                    unset($_GET['ss-action']);
                    $_GET['ss-msg'] = $action;
                    header('Location: ' . html_entity_decode(rex_url::currentBackendPage($_GET)));
                    exit;

                case 'generate_creditnote':
                    $CreditNote = \FriendsOfREDAXO\Simpleshop\Order::create();

                    $CreditNote->calculateCreditNote($Order);
                    $CreditNote->save();

                    unset($_GET['ss-action']);
                    $_GET['ss-msg'] = $action;
                    header('Location: ' . html_entity_decode(rex_url::currentBackendPage($_GET)));
                    exit;

                case 'recalculate_sums':
                    $promotions     = $Order->getValue('promotions', false, []);
                    $order_products = $Order->getProducts(false);

                    $Order->recalculateDocument($order_products, $promotions);
                    $Order->save();

                    unset($_GET['ss-action']);
                    $_GET['ss-msg'] = $action;
                    header('Location: ' . html_entity_decode(rex_url::currentBackendPage($_GET)));
                    exit;

                default:
                    $output = [];
                    $msg    = rex_get('ss-msg', 'string');

                    if ($msg) {
                        echo rex_view::info(rex_i18n::msg("label.msg_{$msg}"));
                    }

                    if ($Customer) {
                        $output[] = '
                        <a href="' . rex_url::currentBackendPage([
                                'table_name' => rex_get('table_name', 'string'),
                                'data_id'    => rex_get('data_id', 'int'),
                                'func'       => rex_get('func', 'string'),
                                'ss-action'  => 'resend_email',
                                'ts'         => time(),
                            ]) . '" class="btn btn-default">
                                <i class="fa fa-send"></i>&nbsp;
                                ' . rex_i18n::msg('label.resend_email') . '
                            </a>
                        ';
                    }
                    $output[] = '
                        <a href="' . rex_url::currentBackendPage([
                            'table_name' => rex_get('table_name', 'string'),
                            'data_id'    => rex_get('data_id', 'int'),
                            'func'       => rex_get('func', 'string'),
                            'ss-action'  => 'recalculate_sums',
                            'ts'         => time(),
                        ]) . '" class="btn btn-default">
                            <i class="fa fa-calculator"></i>&nbsp;
                            ' . rex_i18n::msg('label.recalculate_sums') . '
                        </a>
                    ';
                    $output[] = '
                        <a href="' . rex_url::currentBackendPage([
                            'table_name' => rex_get('table_name', 'string'),
                            'data_id'    => rex_get('data_id', 'int'),
                            'func'       => rex_get('func', 'string'),
                            'ss-action'  => 'generate_pdf',
                            'ts'         => time(),
                        ]) . '" class="btn btn-default">
                            <i class="fa fa-file"></i>&nbsp;
                            PDF drucken
                        </a>
                    ';

                    if ($Order->getValue('status') == 'CA') {
                        $CreditNote = \FriendsOfREDAXO\Simpleshop\Order::getOne(false, [
                            'filter'  => [['ref_order_id', $Order->getId()]],
                            'orderBy' => 'id',
                        ]);

                        if ($CreditNote) {
                            $output[] = '
                                <a href="' . rex_url::currentBackendPage([
                                    'table_name' => 'rex_shop_order',
                                    'data_id'    => $CreditNote->getId(),
                                    'func'       => 'edit',
                                    'ts'         => time(),
                                ]) . '" class="btn btn-primary">
                                    <i class="fa fa-money"></i>&nbsp;
                                    ' . rex_i18n::msg('action.goto_creditnote') . '
                                </a>
                            ';
                        } else {
                            $output[] = '
                                <a href="' . rex_url::currentBackendPage([
                                    'table_name' => rex_get('table_name', 'string'),
                                    'data_id'    => rex_get('data_id', 'int'),
                                    'func'       => rex_get('func', 'string'),
                                    'ss-action'  => 'generate_creditnote',
                                    'ts'         => time(),
                                ]) . '" class="btn btn-default">
                                    <i class="fa fa-money"></i>&nbsp;
                                    ' . rex_i18n::msg('label.generate_creditnote') . '
                                </a>
                            ';
                        }
                    } else if ($Order->valueIsset('ref_order_id')) {
                        $output[] = '
                                <a href="' . rex_url::currentBackendPage([
                                'table_name' => 'rex_shop_order',
                                'data_id'    => $Order->getValue('ref_order_id'),
                                'func'       => 'edit',
                                'ts'         => time(),
                            ]) . '" class="btn btn-primary">
                                    <i class="fa fa-file-text-o"></i>&nbsp;
                                    ' . rex_i18n::msg('action.goto_order') . '
                                </a>
                            ';
                    }
                    $this->params['form_output'][$this->getId()] = '
                        <div class="row nested-panel">
                            <div class="form-group col-xs-12" id="' . $this->getHTMLId() . '">
                                <div>' . implode('', $output) . '</div>
                            </div>
                        </div>
                    ';
                    break;
            }
        }
    }

    public function getDefinitions($values = [])
    {
        return [
            'is_hiddeninlist' => true,
            'is_searchable'   => false,
            'dbtype'          => 'none',
            'type'            => 'value',
            'name'            => 'order_functions',
            'description'     => rex_i18n::msg("yform_values.order_functions_description"),
            'values'          => ['name' => ['type' => 'name', 'label' => rex_i18n::msg("yform_values_defaults_name")],],
        ];
    }
}