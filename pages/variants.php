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

$_FUNC      = \rex_request('func', 'string');
$product_id = rex_get('data_id', 'int');
$product    = \FriendsOfREDAXO\Simpleshop\Product::get($product_id);

if ($_FUNC == 'rm-variants') {
    $sql = \rex_sql::factory();
    $sql->setQuery("DELETE FROM " . Variant::TABLE . " WHERE product_id = :product_id", ['product_id' => $product_id]);
}
elseif ($_FUNC == 'apply-features') {
    $features_ids = explode(',', rex_get('feature_ids', 'string'));
    $current_fids = explode(',', $product->getValue('features'));
    $product->setValue('features', implode(',', array_unique(array_filter(array_merge($features_ids, $current_fids)))));
    $product->save();
}


$features    = $product->getFeatures();
$product_url = \rex_url::backendPage('yform/manager/data_edit', [
    'table_name' => Product::TABLE,
    'data_id'    => $product_id,
    'func'       => 'edit',
]);


if (!$product) {
    echo \rex_view::warning($this->i18n('error.no_product_choosen'));
    return;
}
else if (!$features && Variant::query()->where('product_id', $product_id)->count()) {
    $features = [];
    $variants = Variant::query()->where('product_id', $product_id)->find();

    foreach ($variants as $variant) {
        $_features = explode('|', $variant->getValue('variant_key'));

        foreach ($_features as $feature_id) {
            if (!isset($features[$feature_id])) {
                $features[$feature_id] = FeatureValue::get($feature_id)->getValue('name', true);
            }
        }
    }

    echo \rex_view::error($this->i18n('error.product_has_no_attribute_but_variants') . '<ul><li>' . implode('</li><li>', $features) . '</li></ul>');
    $formElements = [
        ['field' => '<a class="btn btn-apply" href="' . \rex_url::currentBackendPage(['table_name' => Variant::TABLE, 'data_id' => $product_id, 'func' => 'apply-features', 'feature_ids' => implode(',', array_keys($features))]) . '" target="_blank">' . $this->i18n('action.edit_product_add_feature') . '</a>',],
        ['field' => '<a class="btn btn-apply" href="' . \rex_url::currentBackendPage(['table_name' => Variant::TABLE, 'data_id' => $product_id, 'func' => 'rm-variants']) . '">' . $this->i18n('action.remove_all_variants') . '</a>',],
    ];
    $fragment     = new \rex_fragment();
    $fragment->setVar('elements', $formElements, false);
    $buttons = $fragment->parse('core/form/submit.php');

    echo '<form action="" method="post">';
    $fragment = new \rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', $this->i18n('label.what_u_want_todo'));
    $fragment->setVar('buttons', $buttons, false);
    echo $fragment->parse('core/page/section.php');
    echo '</form>';
    return;
}
else if (!$features) {
    echo \rex_view::info(strtr($this->i18n('error.product_has_attribute'), [
        '{{link}}'  => '<a href="' . $product_url . '" class="btn btn-info btn-sm">',
        '{{/link}}' => '</a>',
    ]));
    return;
}

if ($_FUNC == 'save') {
    $vIds = [];
    $data = rex_post('FORM', 'array');

    foreach ($data as $key => $values) {
        $Variant = Variant::getOne(false, [
            'filter' => [
                ['product_id', $product_id],
                ['variant_key', $key],
            ],
        ]);

        if ($Variant) {
            $Variant->setValue('updatedate', date('Y-m-d H:i:s'));
        }
        else {
            $Variant = Variant::create();
            $Variant->setValue('variant_key', $key);
            $Variant->setValue('product_id', $product_id);
        }
        foreach ($values as $name => $value) {
            $Variant->setValue($name, $value);
        }
        $Variant->save();
        $vIds[] = $Variant->getId();
    }
    // remove previously saved variants
    \rex_sql::factory()->setQuery("DELETE FROM " . Variant::TABLE . " WHERE product_id = ? AND id NOT IN(" . implode(',', $vIds) . ")", [$product_id]);
}

// load all columns from yform
$rows         = [];
$featureNames = [];
$featureKeys  = [];
$variants     = Variant::getAll(false, ['filter' => [['product_id', $product_id]]]);

// load all yform fields to place them into columns
list ($labels, $fields) = Variant::be_getYFields();


// calculate the possible variants
foreach ($features as $feature) {
    $_fk         = $featureKeys;
    $featureKeys = [];

    if (count($_fk) == 0) {
        $_fk[] = '';
    }
    foreach ($feature->getValue('values') as $value) {
        $featureNames[$value->getValue('id')] = $value->getName();

        foreach ($_fk as $fk) {
            $vkey   = explode(',', ltrim($fk, ','));
            $vkey[] = $value->getValue('id');
            sort($vkey);

            $featureKeys[] = implode(',', array_filter($vkey));
        }
    }
}

// apply fields/columns to variants
$fragment = new \rex_fragment();
$fragment->setVar('fields', $fields);

foreach ($variants as $Variant) {
    $name   = [];
    $vkey   = $Variant->getValue('variant_key');
    $findex = array_search($vkey, $featureKeys);

    if ($findex !== false) {
        // remove vkey from featurekeys
        unset($featureKeys[$findex]);
    }
    else {
        // by pass invalid feature-keys
        continue;
    }

    foreach (explode(',', $vkey) as $fid) {
        $name[] = $featureNames[$fid];
    }
    $fragment->setVar('vkey', $vkey);
    $fragment->setVar('name', $name);
    $fragment->setVar('Variant', $Variant);

    $rows[] = $fragment->parse('simpleshop/backend/variants/row_item.php');
}
// reset variant
$fragment->setVar('Variant', null);

// add the empty variants
foreach ($featureKeys as $vkey) {
    $name = [];

    foreach (explode(',', $vkey) as $fid) {
        $name[] = $featureNames[$fid];
    }
    $fragment->setVar('vkey', $vkey);
    $fragment->setVar('name', $name);

    $rows[] = $fragment->parse('simpleshop/backend/variants/row_item.php');
}

$fragment = new \rex_fragment();
$fragment->setVar('labels', $labels);
$fragment->setVar('rows', $rows, false);
$content = $fragment->parse('simpleshop/backend/variants/grid.php');

$formElements = [
    ['field' => '<a class="btn btn-abort" href="' . \rex_url::backendPage('yform/manager/data_edit', ['table_name' => Product::TABLE]) . '">' . \rex_i18n::msg('form_abort') . '</a>',],
    ['field' => '<a class="btn btn-abort btn-apply" href="' . $product_url . '">' . $this->i18n('action.edit_product') . '</a>',],
    ['field' => '<button class="btn btn-apply rex-form-aligned" type="submit" name="func" value="save">' . \rex_i18n::msg('form_save') . '</button>',],
];
$fragment     = new \rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

echo '<form action="" method="post" onsubmit="Simpleshop.saveVariants(this)">';
$fragment = new \rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $product->getName() . ' [' . $product_id . ']');
$fragment->setVar('content', $content, false);
$fragment->setVar('buttons', $buttons, false);
echo $fragment->parse('core/page/section.php');
echo '</form>';
