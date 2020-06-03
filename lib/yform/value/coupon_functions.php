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
class rex_yform_value_coupon_functions extends rex_yform_value_abstract
{

    public function processFunctions()
    {
        $count = rex_get('count', 'int');

        list($action1, $action2) = explode('|', rex_get('ss-action', 'string'));

        switch ($action1) {
            case 'msg':
                $msg = strtr(rex_i18n::msg("label.msg_{$action2}"), [
                    '{NUM}' => $count,
                ]);
                echo rex_view::info($msg);
                return;

            case 'clone':
                $dataId = rex_get('data_id', 'int');
                $Coupon = \FriendsOfREDAXO\Simpleshop\Coupon::get($dataId);
                $data   = $Coupon->getData();

                // cloning codes
                for ($i = 0; $i < $count; $i++) {
                    $clone = \FriendsOfREDAXO\Simpleshop\Coupon::create();

                    foreach ($data as $name => $value) {
                        $clone->setValue($name, $value);
                    }
                    $clone->setValue('given_away', 0);
                    $clone->setValue('orders', null);
                    $clone->setValue('code', rex_yform_value_coupon_code::getRandomCode());
                    $clone->setValue('createdate', date('Y-m-d H:i:s'));
                    $clone->save();
                }
                break;
        }

        if (strlen($action1)) {
            \rex_sql::factory()->commit();
            $_GET['ss-action'] = 'msg|' . $_GET['ss-action'];
            header('Location: ' . html_entity_decode(rex_url::currentBackendPage($_GET)));
            exit;
        }
    }

    public function enterObject()
    {
        if (rex::isBackend() && $this->getParam('main_id')) {
            $output = [];
            $table  = $this->getParam('main_table');
            $params = [
                'data_id'    => $this->getParam('main_id'),
                'table_name' => $table,
                'func'       => 'edit',
            ];

            $this->processFunctions();

            // clone coupon
            $output[] = '
                <div class="coupon-functions">
                    <input type="text" class="form-control coupon-clone-count" value="1"/>
                    <div class="inline-text">X</div>
                    <a href="' . rex_url::currentBackendPage(array_merge($_GET, $params, ['ss-action' => 'clone', 'count' => ''])) . '" onclick="return SimpleshopBackend.cloneCoupon(this)" class="btn btn-default">
                        ' . rex_i18n::msg('label.clone_coupon') . '
                    </a>
                </div>
            ';

            // output
            $this->params['form_output'][$this->getId()] .= '
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
            'name'            => 'coupon_functions',
            'description'     => rex_i18n::msg("yform_values.coupon_functions_description"),
            'values'          => ['name' => ['type' => 'name', 'label' => rex_i18n::msg("yform_values_defaults_name")],],
        ];
    }
}