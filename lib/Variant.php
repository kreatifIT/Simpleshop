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

    public function applyProductData($product)
    {
        $variant_data = $this->getData();

        foreach ($variant_data as $key => $value)
        {
            if (in_array($key, ['price', 'reduced_price', 'width', 'height', 'weight', 'length']))
            {
                if ((float) $value <= 0)
                {
                    $this->setValue($key, (float) $product->getValue($key));
                }
            }
            elseif ($value == '' && !in_array($key, ['id', 'product_id', 'variant_key', 'type']))
            {
                $this->setValue($key, $product->getValue($key));
            }
        }
        if ($this->getValue('type') == 'FREE')
        {
            $this->setValue('price', 0);
            $this->setValue('reduced_price', 0);
        }
    }
}