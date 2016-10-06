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
                    $feature      = FeatureValue::query()->where('id', $feature_value_id)->findOne();
                    $variant_data = $_variant->getData();

                    foreach ($variant_data as $key => $value)
                    {
                        if (!in_array($key, ['id', 'product_id', 'feature_value_id']))
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

    public static function getProductByKey($key, $cart_quantity = NULL)
    {
        if (!strlen($key))
        {
            return FALSE;
        }
        list ($product_id, $feature_ids) = explode('|', trim($key, '|'));

        $_this = self::query()->where('id', $product_id)->where('status', 1)->findOne();

        if (!$_this)
        {
            throw new ProductException("No product with ID = " . $product_id . " exists --key:{$key}", 1);
        }
        $features = [];
        $_this->setValue('key', $key);
        $_this->setValue('cart_quantity', $cart_quantity);
        $feature_ids = $feature_ids ? explode(',', $feature_ids) : [];

        foreach ($feature_ids as $feature_id)
        {
            // get variants
            $feature = FeatureValue::query()->where('id', $feature_id)->where('status', 1)->findOne();
            if (!$feature)
            {
                throw new ProductException("No feature with ID = " . $feature_id . " exists --key:{$key}", 2);
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
                throw new ProductException("The variant '" . $vname . "' doesn't exist --key:{$key}", 3);
            }
            $_this->applyVariantData($variant->getData());

            // make some availabilty checks
            if ($_this->getValue('amount') <= 0)
            {
                throw new ProductException("Product not available any more --key:{$key}", 4);
            }
            else if ($_this->getValue('inventory') == 'F' && $_this->getValue('amount') < $_this->getValue('cart_quantity'))
            {
                throw new ProductException("Amount of product is lower than cart quantity --key:{$key}&{$product_id}|{$feature_id}", 5);
            }
            $features[] = $feature;
        }
        $_this->features = $features;
        return $_this;
    }

    private function applyVariantData($variant_data)
    {
        foreach ($variant_data as $key => $value)
        {
            if ($key == 'surcharge' && (float) $value > 0)
            {
                $this->setValue('price', $this->getValue('price') + (float) $value);
                $this->setValue('reduced_price', $this->getValue('reduced_price') + (float) $value);
            }
            elseif ($key == 'amount')
            {
                if ($this->getValue('inventory') == 'F')
                {
                    $this->setValue($key, $value);
                }
            }
            elseif ($value != '' && !in_array($key, ['id', 'product_id', 'feature_value_id', 'surcharge']))
            {
                $this->setValue($key, $value);
            }
        }
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

class ProductException extends \Exception {}