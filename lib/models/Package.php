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


class Package extends Model
{
    const TABLE = 'rex_shop_package';

    public function getAbo()
    {
        $Abo = null;

        if ($this->verifyPackageValidity()) {
            $Abo = PackageCategory::get($this->getValue('abo_id'));
        }
        return $Abo;
    }

    public function verifyPackageValidity()
    {
        $product_ids = $this->getArrayValue('products');

        foreach ($product_ids as $product_id) {
            $product = Product::get($product_id);
            if (
                !$product
                || !$product->isOnline()
                || $product->getValue('amount') < 1
                || \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Package.verifyPackageValidity', false, ['Package' => $this, 'Product' => $product]))
            ) {
                return false;
            }
        }
        return true;
    }
}