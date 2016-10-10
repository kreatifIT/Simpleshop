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

echo \rex_view::title($this->i18n('label.variants'), '');

$_FUNC       = \rex_request('func', 'string');
$product_id  = rex_get('data_id', 'int');
$product     = \FriendsOfREDAXO\Simpleshop\Product::get($product_id);
$features    = $product->getFeatures();
$product_url = \rex_url::backendPage('yform/manager/data_edit', [
    'table_name' => 'rex_shop_product',
    'data_id'    => $product_id,
    'func'       => 'edit',
]);

if (!$product)
{
    echo \rex_view::warning($this->i18n('error.no_product_choosen'));
    return;
}
else if (!$features)
{
    echo \rex_view::info(strtr($this->i18n('error.product_has_attribute'), [
            '{{link}}' => '<a href="' . $product_url . '" class="btn btn-info btn-sm">',
        '{{/link}}' => '</a>',
    ]));
    return;
}
if ($_FUNC == 'save')
{
    // remove previously saved variants
    \rex_sql::factory()->setQuery("DELETE FROM ". Variant::TABLE ." WHERE product_id = ?", [$product_id]);
    $data = rex_post('FORM', 'array');

    foreach ($data as $key => $values)
    {
        if ($values['type'] != 'NE')
        {
            $variant = Variant::create();
            $variant->setValue('variant_key', $key);
            $variant->setValue('product_id', $product_id);

            foreach ($values as $name => $value)
            {
                $variant->setValue($name, $value);
            }
            $variant->save();
        }
    }
}

// load all columns from yform
$columns     = \rex_yform_manager_table::get('rex_shop_product_has_feature')->getFields();
$params      = ['this' => \rex_yform::factory()];
$fields      = [];
$labels      = [];
$variants    = [];
$feature_ids = [];
$rows        = [];

// load all yform fields to place them into columns
foreach ($columns as $column)
{
    $type = $column->getTypeName();
    $name = $column->getName();

    if ($name == 'variant_key' || in_array($type, ['be_manager_relation']))
    {
        continue;
    }
    $values      = [$type];
    $class       = 'rex_yform_value_' . trim($type);
    $field       = new $class();
    $definitions = $field->getDefinitions();

    foreach ($definitions['values'] as $key => $_)
    {
        $values[] = $column->getElement($key);
    }
    $notice = '';

    if (count($values) > 4)
    {
        $notice                     = $values[count($values) - 1];
        $values[count($values) - 1] = '';
    }
    $field->loadParams($params, $values);
    $fields[] = $field;
    $labels[] = [
        'label'  => $field->getLabel(),
        'notice' => $notice,
    ];
}

// calculate the possible variants
foreach ($features as $feature)
{
    $values    = $feature->getValue('values');
    $_variants = $variants;
    $variants  = [];

    if (count($_variants) == 0)
    {
        $_variants[] = '';
    }
    foreach ($values as $value)
    {
        $feature_ids[$value->getValue('id')] = $value->getValue('name_1');

        foreach ($_variants as $variant)
        {
            $variants[] = ltrim($variant . ',' . $value->getValue('id'), ',');
        }
    }
}

// apply fields/columns to variants
foreach ($variants as $variant_key)
{
    $_ids    = explode(',', $variant_key);
    $_name   = [];
    $_fields = [];
    $variant = Variant::query()
        ->where('product_id', $product_id)
        ->where('variant_key', $variant_key)
        ->findOne();

    foreach ($_ids as $id)
    {
        $_name[] = $feature_ids[$id];
    }
    foreach ($fields as $field)
    {
        $field->params['this']->setObjectparams('form_name', $variant_key);
        $field->setId($field->name);
        $field->init();
        $field->setLabel('');
        $field->setValue($variant ? $variant->getValue($field->name) : NULL);
        $field->enterObject();
        $_fields[] = $field->params['form_output'][$field->getId()];
    }
    $fragment = new \rex_fragment();
    $fragment->setVar('fields', $_fields, FALSE);
    $fragment->setVar('name', $_name);
    $rows[] = $fragment->parse('backend/variants/row_item.php');
}

$fragment = new \rex_fragment();
$fragment->setVar('labels', $labels);
$fragment->setVar('rows', $rows, FALSE);
$content = $fragment->parse('backend/variants/grid.php');

$formElements = [
    ['field' => '<a class="btn btn-abort" href="' . \rex_url::backendPage('yform/manager/data_edit', ['table_name' => 'rex_shop_product']) . '">' . \rex_i18n::msg('form_abort') . '</a>',],
    ['field' => '<a class="btn btn-abort btn-apply" href="' . $product_url . '">' . $this->i18n('action.edit_product') . '</a>',],
    ['field' => '<button class="btn btn-apply rex-form-aligned" type="submit" name="func" value="save">' . \rex_i18n::msg('form_save') . '</button>',],
];
$fragment     = new \rex_fragment();
$fragment->setVar('elements', $formElements, FALSE);
$buttons = $fragment->parse('core/form/submit.php');

echo '<form action="" method="post">';
$fragment = new \rex_fragment();
$fragment->setVar('class', 'edit', FALSE);
$fragment->setVar('title', $product->getValue('name_1') . ' [' . $product_id . ']');
$fragment->setVar('content', $content, FALSE);
$fragment->setVar('buttons', $buttons, FALSE);
echo $fragment->parse('core/page/section.php');
echo '</form>';
