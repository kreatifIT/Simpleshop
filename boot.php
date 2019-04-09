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
 */

namespace FriendsOfREDAXO\Simpleshop;

\rex_yform::addTemplatePath(\rex_path::addon('simpleshop', 'ytemplates'));

\rex_yform_manager_dataset::setModelClass(Category::TABLE, Category::class);
\rex_yform_manager_dataset::setModelClass(Coupon::TABLE, Coupon::class);
\rex_yform_manager_dataset::setModelClass(Country::TABLE, Country::class);
\rex_yform_manager_dataset::setModelClass(Customer::TABLE, Customer::class);
\rex_yform_manager_dataset::setModelClass(CustomerAddress::TABLE, CustomerAddress::class);
\rex_yform_manager_dataset::setModelClass(DiscountGroup::TABLE, DiscountGroup::class);
\rex_yform_manager_dataset::setModelClass(Feature::TABLE, Feature::class);
\rex_yform_manager_dataset::setModelClass(FeatureValue::TABLE, FeatureValue::class);
\rex_yform_manager_dataset::setModelClass(Order::TABLE, Order::class);
\rex_yform_manager_dataset::setModelClass(OrderProduct::TABLE, OrderProduct::class);
\rex_yform_manager_dataset::setModelClass(Package::TABLE, Package::class);
\rex_yform_manager_dataset::setModelClass(Product::TABLE, Product::class);
\rex_yform_manager_dataset::setModelClass(ProductHasCategory::TABLE, ProductHasCategory::class);
\rex_yform_manager_dataset::setModelClass(Session::TABLE, Session::class);
\rex_yform_manager_dataset::setModelClass(Tax::TABLE, Tax::class);
\rex_yform_manager_dataset::setModelClass(Variant::TABLE, Variant::class);


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