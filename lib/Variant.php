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

class Variant extends Model
{
    const TABLE = "rex_shop_product_has_feature";

    public static function getByVariantKey($key)
    {
        list ($product_id, $variant_key) = explode('|', $key);
        return self::query()
            ->where('product_id', $product_id)
            ->where('variant_key', $variant_key)
            ->findOne();
    }

    public function applyProductData()
    {
        $_product = Product::get($this->getValue('product_id'));
        $product  = clone $_product;
        $product->applyVariantData($this->getData());
        $product_data = $product->getData();

        foreach ($product_data as $key => $value)
        {
            $this->setValue($key, $value);
        }
    }
}