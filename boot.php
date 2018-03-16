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
    Package::TABLE         => Package::class,
    PackageCategory::TABLE => PackageCategory::class,
    Product::TABLE         => Product::class,
    Session::TABLE         => Session::class,
    Tax::TABLE             => Tax::class,
    Variant::TABLE         => Variant::class,
]);
$table_classes = $this->getConfig('table_classes');

if (!$table_classes) {
    Utils::ext_register_tables();
    $table_classes = $this->getConfig('table_classes');
}
foreach ($table_classes as $table => $class) {
    \rex_yform_manager_dataset::setModelClass($table, $class);
}

$include_files = glob($this->getPath('functions/*.inc.php'));
$include_files = array_merge($include_files, glob($this->getPath('inc/*.inc.php')));

foreach ($include_files as $include_file) {
    include_once $include_file;
}

if (\rex::isBackend()) {
    $include_files = glob($this->getPath('inc/backend/*.inc.php'));

    foreach ($include_files as $include_file) {
        include_once $include_file;
    }
}