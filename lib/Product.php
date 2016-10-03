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

class Product extends \rex_yform_manager_dataset
{
    protected $features = [];

    public function getFeatures()
    {
        return $this->features;
    }

    public static function getProductByKey($key)
    {
        if (!strlen($key))
        {
            return false;
        }
        list ($product_id, $feature_ids) = explode('|', trim($key, '|'));

        $_this = self::get($product_id);

        if (!$_this)
        {
            throw new \ErrorException("No product with ID = " . $product_id . " exists", 1);
        }
        $_this->setValue('key', $key);
        $_this->setValue('cart_quantity', 0);
        $feature_ids = $feature_ids ? explode(',', $feature_ids) : [];

        foreach ($feature_ids as $feature_id)
        {
            // get variants
            $feature = Feature::get($feature_id);
            if (!$feature)
            {
                throw new \ErrorException("No feature with ID = " . $feature_id . " exists", 2);
            }
            $variant = Variant::query()
                ->where('product_id', $product_id)
                ->where('feature_value_id', $feature_id)
                ->findOne();

            if (!$variant)
            {
                // the given combination does not exist!
                $lang_id = \rex_clang::getCurrentId();
                $vname   = $_this->getValue('name_' . $lang_id) . "' - '" . $feature->getValue('name_' . $lang_id);
                throw new \ErrorException("The variant '" . $vname . "' doesn't exist", 3);
            }
            $_this->applyVariantData($variant->getData());
            $_this->features[] = $feature;
        }
        return $_this;
    }

    private function applyVariantData($variant_data)
    {
        foreach ($variant_data as $key => $value)
        {
            if ($key == 'surcharge' && (float) $value > 0)
            {
                $this->setValue('price', $this->getValue('price') + (float) $value);
            }
            if ($value != '' && !in_array($key, ['id', 'product_id', 'feature_value_id', 'surcharge']))
            {
                $this->setValue($key, $value);
            }
        }
    }

    public function getPrice($formated = TRUE)
    {
        $base_price = $this->getValue('price');
        if ($formated)
        {
            $conf       = localeconv();
            $base_price = number_format($base_price, 2, $conf['mon_decimal_point'], $conf['mon_thousands_sep']);
        }
        return $base_price;
    }
}