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

use Url\Rewriter\Yrewrite;
use Url\Url;

class Product extends \rex_yform_manager_dataset
{
    protected $variants = NULL;

    public function getVariants()
    {
        if ($this->variants === NULL)
        {
            $this->variants = $this->_getVariants($feature_key);
        }
//        else if ($feature_key && !array_key_exists($feature_key, $this->variants))
//        {
//            $_variants = $this->_getVariants($feature_key);
//            $this->variants[$feature_key] = $_variants[$feature_key];
//        }
//        return $feature_key ? $this->variants[$feature_key] : $this->variants;
        return $this->variants;
    }

    private function _getVariants($feature_key = NULL)
    {
        $variants       = [];
        $feature_values = [];
        $_groupped_var  = [];
        $_variants      = Variant::query()->where('product_id', $this->getValue('id'))->find();
        $query          = Feature::query();

        if ($feature_key)
        {
            $query->where('key', $feature_key);
        }
        $features = $query->find();

        if (count($_variants))
        {
            foreach ($features as $index => $feature)
            {
                $values         = strlen($feature->getValue('values')) ? explode(',', $feature->getValue('values')) : [];
                $feature_values = $feature_values + array_fill_keys($values, $index);
            }
            foreach ($_variants as $_variant)
            {
                $feature_value_id = $_variant->getValue('feature_value_id');

                if (array_key_exists($feature_value_id, $feature_values))
                {
                    $feature          = FeatureValue::get($feature_value_id);
                    $variant_data     = $_variant->getData();

                    foreach ($variant_data as $key => $value)
                    {
                        if (!in_array ($key, ['id', 'product_id', 'feature_value_id']))
                        {
                            $feature->setValue($key, $value);
                        }
                    }
                    $_groupped_var[$feature_values[$feature_value_id]][] = $feature;
                }
            }
            foreach ($_groupped_var as $index => $_variants)
            {
                $feature = $features[$index];
                $feature->setValue('variants', $_variants);
                $variants[$feature->getValue('key')] = $feature;
            }
        }
        return $variants;
    }

    public static function getProductByKey($key)
    {
        if (!strlen($key))
        {
            return FALSE;
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
            $feature = FeatureValue::get($feature_id);
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
            $base_price = format_price($base_price);
        }
        return $base_price;
    }

    public function getUrl($lang_id = NULL)
    {
        return rex_getUrl(NULL, $lang_id, ['product_id' => $this->getValue('id')]);
    }

    public function generatePath($lang_id, $path = '')
    {
        $_paths  = [];
        $parents = Category::get($this->getValue('category_id'))->getParentTree();

        foreach ($parents as $parent)
        {
            $_paths[] = Url::getRewriter()->normalize($parent->getValue('name_' . $lang_id));
        }
        $_paths[] = $path;
        return implode('/', $_paths);
    }


}