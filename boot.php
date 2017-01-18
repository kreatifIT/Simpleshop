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
 *
 *
 * // Set Variant by setting/overwriting the simpleshop.product_variants property [NULL|variants]
 * \rex::setProperty('simpleshop.product_variants', 'variant');
 */

namespace FriendsOfREDAXO\Simpleshop;

$this->setProperty('table_classes', [
    Category::TABLE        => Category::class,
    Coupon::TABLE          => Coupon::class,
    Country::TABLE         => Country::class,
    Customer::TABLE        => Customer::class,
    CustomerAddress::TABLE => CustomerAddress::class,
    DiscountGroup::TABLE   => DiscountGroup::class,
    Feature::TABLE         => Feature::class,
    FeatureValue::TABLE    => FeatureValue::class,
    Order::TABLE           => Order::class,
    OrderProduct::TABLE    => OrderProduct::class,
    Product::TABLE         => Product::class,
    Category::TABLE        => Category::class,
    Session::TABLE         => Session::class,
    Tax::TABLE             => Tax::class,
    Variant::TABLE         => Variant::class,
]);
$table_classes = $this->getConfig('table_classes');

if (!$table_classes)
{
    Utils::ext_register_tables();
    $table_classes = $this->getConfig('table_classes');
}
foreach ($table_classes as $table => $class)
{
    \rex_yform_manager_dataset::setModelClass($table, $class);
}

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
        case Category::TABLE:
            $path = Category::get($data['id'])->generatePath($lang_id);
            break;
        case Product::TABLE:
            $path = Product::get($data['id'])->generatePath($lang_id, $path);
            break;
    }
    return $path;
});

\rex_extension::register('YFORM_DATA_LIST', function ($params)
{
    $list         = $params->getSubject();
    $list_params  = $list->getParams();
    $variant_type = \rex::getProperty('simpleshop.product_variants');

    if ($variant_type && $list_params['table_name'] == Product::TABLE)
    {
        $list->addColumn('variants', $this->i18n('action.manage_variants'), count($list->getColumnNames()) - 2);
        $list->setColumnLabel('variants', $this->i18n('label.variants'));
        $list->setColumnParams('variants', [
            'page'       => "simpleshop/{$variant_type}",
            'func'       => 'edit',
            'data_id'    => '###id###',
            'table_name' => Variant::TABLE,
        ]);
    }
    return $list;
});

if (\rex::isBackend())
{
    \rex::setProperty('simpleshop.product_variants', 'variants');

    // TODO: to review
    \rex_extension::register('YFORM_DATA_LIST_SQL', function ($params)
    {
        $sql   = $params->getSubject();
        $table = $params->getParams()['table'];

        if ($table->getTableName() == Category::TABLE)
        {
            if (stripos($sql, 'where') === FALSE)
            {
                $sql = preg_replace('/ORDER\sBY/i', 'where `parent_id` = \'\' ORDER BY', $sql);
            }
        }
        return $sql;
    });

    \rex_extension::register('PACKAGES_INCLUDED', function ()
    {
        if ($this->getProperty('compile'))
        {
            $compiler   = new \rex_scss_compiler();
            $scss_files = \rex_extension::registerPoint(new \rex_extension_point('BE_STYLE_SCSS_FILES', [$this->getPath('assets/styles.scss')]));
            $compiler->setScssFile($scss_files);
            $compiler->setCssFile($this->getPath('assets/styles.css'));
            $compiler->compile();
            \rex_file::copy($this->getPath('assets/styles.css'), $this->getAssetsPath('css/styles.css'));
        }
    });
    // CSS
    \rex_view::addCssFile($this->getAssetsUrl('css/styles.css'));
}