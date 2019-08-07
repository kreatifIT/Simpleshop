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
        if (rex::isBackend() && $this->getParam('main_id')) {
            $Order            = \FriendsOfREDAXO\Simpleshop\Order::get($this->getParam('main_id'));
            $CustomerData     = $Order->getCustomerData();
            $table            = $this->getParam('main_table');
            $main_id          = $this->getParam('main_id');
            $use_invoicing    = \FriendsOfREDAXO\Simpleshop\Utils::getSetting('use_invoicing', false);
            $use_packing_list = \FriendsOfREDAXO\Simpleshop\Utils::getSetting('packing_list_printing', false);

            if (!$CustomerData || $CustomerData->getId() != $Order->getValue('customer_id')) {
                $Order->save();
                $CustomerData = $Order->get('customer_data');
            }

            $Customer = $Order->getValue('customer_id') ? \FriendsOfREDAXO\Simpleshop\Customer::get($Order->getValue('customer_id')) : $Order->getInvoiceAddress();;

            if (strlen($table) && $this->getParam('send') == 0 && $this->getParam('main_id') > 0) {
                $action = rex_get('ss-action', 'string');

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

                    case 'download_xml':
                        rex_response::cleanOutputBuffers();
                        $iDateTs = strtotime($Order->getValue('createdate'));
                        $iDate   = date('Y-m-d', $iDateTs);

                        $XMLi = $Order->getXML();

                        if ($XMLi) {
                            $XMLi->buildXML();
                            $xml = Wildcard::parse($XMLi->getXMLFormated());

                            $folder   = rex_path::addonData('simpleshop', 'invoice_xml/' . date('Y', $iDateTs) . '/' . date('m', $iDateTs));
                            $filename = rex::getServerName() . '_' . $iDate . '__' . $Order->getValue('invoice_num') . '.xml';

                            rex_dir::create($folder, true);
                            rex_file::put($folder . '/' . $filename, $xml);

                            rex_response::setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
                            rex_response::sendContent($xml, 'text/xml');
                            exit;
                        }
                        break;

                    case 'generate_packing_list':
                        rex_response::cleanOutputBuffers();
                        $PDF = $Order->getPackingListPDF(false);
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
                }
            }

            $output = [];
            $msg    = rex_get('ss-msg', 'string');

            if ($msg) {
                echo rex_view::info(rex_i18n::msg("label.msg_{$msg}"));
            }

            if ($Customer) {
                $output[] = '
                <a href="' . rex_url::currentBackendPage([
                        'table_name' => $table,
                        'data_id'    => $main_id,
                        'func'       => rex_request('func', 'string'),
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
                    'table_name' => $table,
                    'data_id'    => $main_id,
                    'func'       => rex_request('func', 'string'),
                    'ss-action'  => 'recalculate_sums',
                    'ts'         => time(),
                ]) . '" class="btn btn-default">
                    <i class="fa fa-calculator"></i>&nbsp;
                    ' . rex_i18n::msg('label.recalculate_sums') . '
                </a>
            ';
            if ($use_packing_list) {
                $output[] = '
                    <a href="' . rex_url::currentBackendPage([
                        'table_name' => $table,
                        'data_id'    => $main_id,
                        'func'       => rex_request('func', 'string'),
                        'ss-action'  => 'generate_packing_list',
                        'ts'         => time(),
                    ]) . '" class="btn btn-default">
                        <i class="fa fa-ship"></i>&nbsp;
                        Lieferschein drucken
                    </a>
                ';
            }

            if ($use_invoicing) {
                $output[] = '
                    <a href="' . rex_url::currentBackendPage([
                        'table_name' => $table,
                        'data_id'    => $main_id,
                        'func'       => rex_request('func', 'string'),
                        'ss-action'  => 'generate_pdf',
                        'ts'         => time(),
                    ]) . '" class="btn btn-default">
                        <i class="fa fa-file"></i>&nbsp;
                        PDF drucken
                    </a>
                ';
                $output[] = '
                    <a href="' . rex_url::currentBackendPage([
                        'table_name' => $table,
                        'data_id'    => $main_id,
                        'func'       => rex_request('func', 'string'),
                        'ss-action'  => 'download_xml',
                        'ts'         => time(),
                    ]) . '" class="btn btn-default">
                                <i class="fa fa-code"></i>&nbsp;
                                XML downloaden
                            </a>
                        ';
            }

            if ($Order->getValue('status') == 'CA') {
                $CreditNote = \FriendsOfREDAXO\Simpleshop\Order::getOne(false, [
                    'filter'  => [['ref_order_id', $Order->getId()]],
                    'orderBy' => 'id',
                ]);

                if ($CreditNote) {
                    $output[] = '
                        <a href="' . rex_url::currentBackendPage([
                            'table_name' => $table,
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
                            'table_name' => $table,
                            'data_id'    => $main_id,
                            'func'       => rex_request('func', 'string'),
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
                        'table_name' => $table,
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