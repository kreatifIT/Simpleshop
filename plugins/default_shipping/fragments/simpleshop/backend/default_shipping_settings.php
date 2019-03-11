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

$Settings = $this->getVar('Settings');
$key = $this->getVar('key');
$countries = Country::query()
    ->orderBy('status', 'desc')
    ->orderBy('prio')
    ->find();

$yfparams         = \rex_yform::factory()->objparams;
$yfparams['this'] = \rex_yform::factory();


if (!empty($_POST)) {
    $_costs = [];
    $costs  = [];

    foreach ($Settings['FORM'] as $country_key => $values) {
        list($country_id, $index) = explode('.', $country_key);
        $_costs[$country_id][$index] = $values;
    }
    foreach ($_costs as $country_id => $values) {
        foreach ($values[0] as $index => $value) {
            if (trim($value) != '') {
                $costs[$country_id][$value] = $values[1][$index];
            }
        }
    }
    $Settings['costs'] = $costs;

    unset($Settings['FORM']);
    \rex::setConfig($key, $Settings);
}

?>
    <fieldset>
        <dl class="rex-form-group form-group">
            <dt><?= $this->i18n('default_shipping.general_costs'); ?>:</dt>
            <dd>
                <input type="text" class="form-control expanded" name="general_costs" value="<?= from_array($Settings, 'general_costs') ?>"/>
            </dd>
        </dl>
        <dl class="rex-form-group form-group">
            <dt><?= $this->i18n('default_shipping.general_free_shipping'); ?>:</dt>
            <dd>
                <input type="text" class="form-control expanded" name="general_free_shipping" value="<?= from_array($Settings, 'general_free_shipping') ?>"/>
            </dd>
        </dl>
    </fieldset>

<?php if ($countries): ?>
    <fieldset>
        <legend><?= $this->i18n('default_shipping.costs_per_country'); ?></legend>

        <table class="table settings-table">
            <?php foreach ($countries as $country): ?>
                <tr class="<?= $country->getValue('status') == 0 ? 'offline' : '' ?>">
                    <td><?= $country->getName() ?></td>
                    <td>
                        <?php
                        $field = new \rex_yform_value_be_table();
                        $name  = $country->getId();
                        $field->loadParams($yfparams, [
                            0         => 'be_table',
                            'name'    => $name,
                            'label'   => '',
                            'columns' => implode(',', [
                                'number|min_order|' . $this->i18n('default_shipping.min_order') . '|10|2|0|||',
                                'number|shipping_costs|' . $this->i18n('default_shipping.shipping_costs') . '|10|2|0|||',
                            ]),
                        ]);
                        $field->setId($name);

                        if (isset($Settings['costs'][$country->getId()])) {
                            $_value = [];

                            foreach ($Settings['costs'][$country->getId()] as $col1 => $col2) {
                                $_value[] = [$col1, $col2];
                            }
                            $field->setValue(json_encode($_value));
                        } else {
                            $field->setValue('{}');
                        }
                        $field->init();
                        $field->enterObject();

                        echo $field->params['form_output'][$name];
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    </fieldset>
<?php endif; ?>