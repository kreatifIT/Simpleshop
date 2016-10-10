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

\rex_yform_manager_dataset::setModelClass('rex_shop_category', Category::class);
\rex_yform_manager_dataset::setModelClass('rex_customer', Customer::class);
\rex_yform_manager_dataset::setModelClass('rex_shop_feature', Feature::class);
\rex_yform_manager_dataset::setModelClass('rex_shop_feature_values', FeatureValue::class);
\rex_yform_manager_dataset::setModelClass('rex_shop_product', Product::class);
\rex_yform_manager_dataset::setModelClass('rex_shop_category', Category::class);
\rex_yform_manager_dataset::setModelClass('rex_shop_session', Session::class);
\rex_yform_manager_dataset::setModelClass('rex_shop_tax', Tax::class);
\rex_yform_manager_dataset::setModelClass('rex_shop_product_has_feature', Variant::class);

$include_files = glob($this->getPath('functions/*.inc.php'));

foreach ($include_files as $include_file)
{
    require_once $include_file;
}

\rex_extension::register('FE_OUTPUT', function ($params)
{
    // api endpoint
    $api_result = \rex_api_simpleshop_api::factory();
    if ($api_result && $api_result->hasMessage())
    {
        header('Content-Type: application/json');
        echo $api_result->getResult()->toJSON();
        exit;
    }
    else
    {
        // save url to session
        $session = Session::getSession();
        $session->writeSession([
            'last_url' => rex_getUrl(),
        ]);
    }
    return $params->getSubject();
});

\rex_extension::register('URL_GENERATOR_PATH_CREATED', function ($params)
{
    $path    = $params->getSubject();
    $data    = $params->getParam('data');
    $lang_id = $params->getParam('clang_id');

    switch ($params->getParam('table')->name)
    {
        case 'rex_shop_category':
            $path = Category::get($data['id'])->generatePath($lang_id);
            break;
        case 'rex_shop_product':
            $path = Product::get($data['id'])->generatePath($lang_id, $path);
            break;
    }
    return $path;
});

\rex_extension::register('YFORM_DATA_LIST', function ($params)
{
    $list        = $params->getSubject();
    $list_params = $list->getParams();

    if ($list_params['table_name'] == 'rex_shop_product')
    {
        $list->addColumn('variants', $this->i18n('action.manage_variants'), count($list->getColumnNames()) - 2);
        $list->setColumnLabel('variants', $this->i18n('label.variants'));
        $list->setColumnParams('variants', [
            'page'       => 'simpleshop/variants',
            'func'       => 'edit',
            'data_id'    => '###id###',
            'table_name' => 'rex_shop_product_has_feature',
        ]);
    }
    return $list;
});
