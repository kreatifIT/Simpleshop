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
class rex_yform_value_product_functions extends rex_yform_value_abstract
{

    public function enterObject()
    {
        if (rex::isBackend() && $this->getParam('main_id')) {
            $msg     = '';
            $output  = [];
            $Product = \FriendsOfREDAXO\Simpleshop\Product::get($this->getParam('main_id'));

            if ($Product) {
                $action = rex_get('sa', 'string');

                switch ($action) {
                    case 'generate_pdf':
                        $msg = '';
                        break;
                }

                $output[] = '
                    <a href="' . $Product->getUrl(['ts' => time()]) . '" class="btn btn-default" target="_blank">
                        <i class="fa fa-external-link"></i>&nbsp;
                        ' . rex_i18n::msg('label.goto_product') . '
                    </a>
                ';
            }


            if ($msg != '') {
                echo rex_view::info(rex_i18n::msg("label.msg_{$msg}"));
            }
            if (count($output)) {
                $this->params['form_output'][$this->getId()] = '
                    <div class="row nested-panel">
                        <div class="form-group col-xs-12" id="' . $this->getHTMLId() . '">
                            <div>' . implode('', $output) . '</div>
                        </div>
                    </div>
                ';
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
            'name'            => 'product_functions',
            'values'          => [
                'name' => ['type' => 'name', 'label' => rex_i18n::msg("yform_values_defaults_name")],
            ],
        ];
    }
}