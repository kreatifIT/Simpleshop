<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

use Kreatif\Model\Country;

$Settings  = $this->getVar('Settings');
$key       = $this->getVar('key');
$countries = Country::query()
    ->orderBy('status', 'desc')
    ->orderBy('prio')
    ->find();

$yfparams         = \rex_yform::factory()->objparams;
$yfparams['this'] = \rex_yform::factory();

// Each per-country row now carries a 3rd column (use_dfi) besides
// min_order/cost, so the flat "value per min_order" map from
// default_shipping becomes ['cost' => ..., 'use_dfi' => bool].
if (!empty($_POST)) {
    $_costs = [];
    $costs  = [];

    if (isset($Settings['FORM'])) {
        foreach ($Settings['FORM'] as $country_key => $values) {
            list($country_id, $index) = explode('.', $country_key);
            $_costs[$country_id][$index] = $values;
        }
        foreach ($_costs as $country_id => $values) {
            foreach ((array) $values[0] as $index => $value) {
                if (trim($value) != '') {
                    $costs[$country_id][$value] = [
                        'cost'    => $values[1][$index] ?? 0,
                        'use_dfi' => !empty($values[2][$index]),
                    ];
                }
            }
        }
    }
    $Settings['costs'] = $costs;

    unset($Settings['FORM']);
    \rex::setConfig($key, $Settings);
}

?>
<fieldset>
    <legend><?= \rex_i18n::msg('dfi_shipping.api_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= \rex_i18n::msg('dfi_shipping.api_token'); ?>:</dt>
        <dd>
            <input type="text" class="form-control expanded" name="api_token" value="<?= from_array($Settings, 'api_token') ?>"/>
        </dd>
    </dl>
</fieldset>

<fieldset>
    <legend><?= \rex_i18n::msg('dfi_shipping.general_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= \rex_i18n::msg('dfi_shipping.general_costs'); ?>:</dt>
        <dd>
            <input type="text" class="form-control expanded" name="general_costs" value="<?= from_array($Settings, 'general_costs') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= \rex_i18n::msg('dfi_shipping.general_free_shipping'); ?>:</dt>
        <dd>
            <input type="text" class="form-control expanded" name="general_free_shipping" value="<?= from_array($Settings, 'general_free_shipping') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= \rex_i18n::msg('dfi_shipping.use_dfi'); ?>:</dt>
        <dd>
            <div class="form-label">
                <label class="form-label">
                    <input type="checkbox" name="general_use_dfi" value="1" <?= from_array($Settings, 'general_use_dfi') == 1 ? 'checked="checked"' : ''; ?>/>
                    <?= \rex_i18n::msg('dfi_shipping.general_use_dfi_notice'); ?>
                </label>
            </div>
        </dd>
    </dl>
</fieldset>

<?php if ($countries): ?>
    <fieldset>
        <legend><?= \rex_i18n::msg('dfi_shipping.costs_per_country'); ?></legend>

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
                                'number|min_order|' . \rex_i18n::msg('dfi_shipping.min_order') . '|10|2|0|||',
                                'number|cost|' . \rex_i18n::msg('dfi_shipping.shipping_costs') . '|10|2|0|||',
                                'checkbox|use_dfi|' . \rex_i18n::msg('dfi_shipping.use_dfi') . '|0||',
                            ]),
                        ]);
                        $field->setId($name);

                        if (isset($Settings['costs'][$country->getId()])) {
                            $_value = [];

                            foreach ($Settings['costs'][$country->getId()] as $min_order => $row) {
                                $_value[] = [$min_order, $row['cost'], !empty($row['use_dfi']) ? 1 : 0];
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

        <p class="text-muted"><?= \rex_i18n::msg('dfi_shipping.costs_per_country_notice'); ?></p>
    </fieldset>
<?php endif; ?>

<style>
    .dfi-cost-readonly {
        background-color: #eee !important;
        color: #999 !important;
        cursor: not-allowed !important;
        opacity: 1 !important;
    }
</style>

<script>
    (function () {
        // Column order in the DOM doesn't reliably match the definition order
        // for be_table rows added via "Reihe hinzufügen" (its JS row template
        // reuses shared field objects across columns), so td:nth-child(n) can
        // land on the wrong column. Matching on the rendered data-title
        // (= the column label) is robust regardless of that quirk.
        var DFI_COST_LABEL = <?= json_encode(\rex_i18n::msg('dfi_shipping.shipping_costs')) ?>;

        // "readonly" instead of "disabled": disabled inputs are dropped from
        // the POST entirely, which would desync the be_table columns'
        // index-based arrays as soon as rows have mixed checkbox states.
        // readonly keeps the value in the submit while blocking edits; the
        // dfi-cost-readonly class gives it the same greyed-out look a real
        // "disabled" input would have.
        function toggleCostField(checkbox) {
            var row = checkbox.closest('tr');
            var costCell = row ? row.querySelector('td[data-title="' + DFI_COST_LABEL + '"]') : null;
            var costInput = costCell ? costCell.querySelector('input') : null;

            setReadonly(costInput, checkbox.checked);
        }

        function setReadonly(input, readonly) {
            if (!input) {
                return;
            }
            if (readonly) {
                input.setAttribute('readonly', 'readonly');
                input.classList.add('dfi-cost-readonly');
            } else {
                input.removeAttribute('readonly');
                input.classList.remove('dfi-cost-readonly');
            }
        }

        document.querySelectorAll('.settings-table input[type="checkbox"]').forEach(toggleCostField);

        document.addEventListener('change', function (e) {
            if (e.target.matches && e.target.matches('.settings-table input[type="checkbox"]')) {
                toggleCostField(e.target);
            }
        });

        if (window.jQuery) {
            jQuery(document).on('be_table:row-added', function (e, tr) {
                jQuery(tr).find('input[type="checkbox"]').each(function () {
                    toggleCostField(this);
                });
            });
        }

        var generalCheckbox  = document.querySelector('input[name="general_use_dfi"]');
        var generalCostInput = document.querySelector('input[name="general_costs"]');

        if (generalCheckbox) {
            setReadonly(generalCostInput, generalCheckbox.checked);
            generalCheckbox.addEventListener('change', function () {
                setReadonly(generalCostInput, generalCheckbox.checked);
            });
        }
    })();
</script>
