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

use Url\Url;

class Product extends \rex_yform_manager_dataset
{
    protected $__feature_variants = NULL;
    protected $__variants         = NULL;
    protected $__features         = NULL;


    public function getFeatures()
    {
        if ($this->__features === NULL)
        {
            $features         = trim($this->getValue('features'));
            $this->__features = strlen($features) ? $this->_getFeatures(explode(',', $features)) : [];
        }
        return $this->__features;
    }

    private function _getFeatures($feature_ids)
    {
        $result         = [];
        $feature_values = [];
        $all_features   = Feature::getAll();

        foreach ($all_features as $feature)
        {
            $values         = strlen($feature->getValue('values')) ? explode(',', $feature->getValue('values')) : [];
            $feature_values = $feature_values + array_fill_keys($values, $feature->getValue('id'));
        }
        foreach ($feature_ids as $feature_value_id)
        {
            if (array_key_exists($feature_value_id, $feature_values))
            {
                if (!isset ($result[$feature_values[$feature_value_id]]))
                {
                    $_feature = Feature::get($feature_values[$feature_value_id]);
                    $_feature->setValue('values', []);
                    $result[$feature_values[$feature_value_id]] = $_feature;
                }
                $_feature = $result[$feature_values[$feature_value_id]];
                $values   = $_feature->getValue('values');
                $values[] = FeatureValue::get($feature_value_id);
                $_feature->setValue('values', $values);
            }
        }
        $_result = $result;
        $result  = [];

        foreach ($_result as $r)
        {
            $result[$r->getValue('key')] = $r;
        }
        return $result;
    }

    public function getFeatureVariants()
    {
        if ($this->__feature_variants === NULL)
        {
            $min_amount   = NULL;
            $feature_ids  = [];
            $feature_keys = [];
            $_variants    = Variant::query()
                ->where('product_id', $this->getValue('id'))
                ->where('type', 'NE', '!=')
                ->find();

            foreach ($_variants as $variant)
            {
                $key  = $variant->getValue('variant_key');
                $_ids = explode(',', $key);
                $variant->applyProductData($this);
                $amount     = $variant->getValue('amount');
                $min_amount = $min_amount === NULL || $amount > 0 && $amount < $min_amount ? $amount : $min_amount;

                foreach ($_ids as $id)
                {
                    $feature_keys[$id][$key] = $variant;
                    $feature_ids[]           = $id;
                }
            }
            $feature_ids = array_unique($feature_ids);
            $features    = $this->_getFeatures($feature_ids);

            foreach ($features as $feature)
            {
                foreach ($feature->values as $value)
                {
                    $min_amount      = NULL;
                    $value->variants = $feature_keys[$value->getValue('id')];

                    foreach ($value->variants as $variant)
                    {
                        $amount     = $variant->getValue('amount');
                        $min_amount = $min_amount === NULL || $amount > 0 && $amount < $min_amount ? $amount : $min_amount;
                    }
                    $value->setValue('min_amount', (int) $min_amount);
                }
            }
            $this->__feature_variants = $features;
            $this->setValue('min_amount', $min_amount);
        }
        return $this->__feature_variants;
    }

    public function getVariants()
    {
//        if ($this->__variants === NULL)
//        {
//            $variants    = [];
//            $feature_ids = [];
//            $_variants   = Variant::query()->where('product_id', $this->getValue('id'))->find();
//
//            foreach ($_variants as $variant)
//            {
//                $feature_ids = array_merge($feature_ids, explode(',', $variant->getValue('variant_key')));
//            }
//            $feature_ids = array_unique($feature_ids);
//            $features    = $this->_getFeatures($feature_ids);
//
//            if ($features)
//            {
//                foreach ($_variants as $variant)
//                {
//                    $key            = $variant->getValue('variant_key');
//                    $variants[$key] = $variant;
//                }
//            }
//            pr($variants);
//            exit;
//
//            if (count($features))
//            {
//                foreach ($features as $index => $feature)
//                {
//                    $values         = strlen($feature->getValue('values')) ? explode(',', $feature->getValue('values')) : [];
//                    $feature_values = $feature_values + array_fill_keys($values, $index);
//                }
//                foreach ($_variants as $_variant)
//                {
//                    $feature_value_id = $_variant->getValue('feature_value_id');
//
//                    if (array_key_exists($feature_value_id, $feature_values))
//                    {
//                        $feature      = FeatureValue::query()->where('id', $feature_value_id)->findOne();
//                        $variant_data = $_variant->getData();
//
//                        foreach ($variant_data as $key => $value)
//                        {
//                            if (!in_array($key, ['id', 'product_id', 'feature_value_id']))
//                            {
//                                $feature->setValue($key, $value);
//                            }
//                        }
//                        $_groupped_var[$feature_values[$feature_value_id]][] = $feature;
//                    }
//                }
//                foreach ($_groupped_var as $index => $_variants)
//                {
//                    $feature = $features[$index];
//                    $feature->setValue('variants', $_variants);
//                    $variants[$feature->getValue('key')] = $feature;
//                }
//            }
//            $this->__variants = $variants;
//        }
//        return $this->__variants;
    }

    public static function getProductByKey($key, $cart_quantity = NULL)
    {
        if (!strlen($key))
        {
            return FALSE;
        }
        list ($product_id, $variant_key) = explode('|', trim($key, '|'));

        $_this = self::query()->where('id', $product_id)->where('status', 1)->findOne();

        if (!$_this)
        {
            throw new ProductException("No product with ID = " . $product_id . " exists --key:{$key}", 1);
        }
        $features = [];
        $_this->setValue('key', $key);
        $_this->setValue('cart_quantity', $cart_quantity);
        $feature_ids = $variant_key ? explode(',', $variant_key) : [];

        if (count($feature_ids))
        {
            $variant = Variant::query()
                ->where('product_id', $product_id)
                ->where('type', 'NE', '!=')
                ->where('variant_key', $variant_key)
                ->findOne();

            if (!$variant)
            {
                // the given combination does not exist!
                throw new ProductException("The variant doesn't exist --key:{$key}", 3);
            }
            $_this->applyVariantData($variant->getData());
        }

        foreach ($feature_ids as $feature_id)
        {
            // get variants
            $feature = FeatureValue::query()->where('id', $feature_id)->where('status', 1)->findOne();
            if (!$feature)
            {
                throw new ProductException("No feature with ID = " . $feature_id . " exists --key:{$key}", 2);
            }
            else if ($_this->getValue('amount') <= 0)
            {
                // make some availabilty checks
                throw new ProductException("Product not available any more --key:{$key}", 4);
            }
            else if ($_this->getValue('inventory') == 'F' && $_this->getValue('amount') < $_this->getValue('cart_quantity'))
            {
                throw new ProductException("Amount of product is lower than cart quantity --key:{$key}", 5);
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
            if (in_array($key, ['price', 'reduced_price', 'width', 'height', 'weight', 'length']))
            {
                if ((float) $value > 0)
                {
                    $this->setValue($key, (float) $value);
                }
            }
            elseif ($key == 'amount')
            {
                if ($this->getValue('inventory') == 'F' && $value != '')
                {
                    $this->setValue($key, $value);
                }
            }
            elseif ($value != '' && !in_array($key, ['id', 'product_id', 'type']))
            {
                $this->setValue($key, $value);
            }
        }
        if ($variant_data['type'] == 'FREE')
        {
            // if variant is free set price to zero
            $this->setValue('price', 0);
            $this->setValue('reduced_price', 0);
        }
        else if ((float) $variant_data['price'] > 0 && (float) $variant_data['reduced_price'] <= 0)
        {
            // if variant has a price
            $this->setValue('reduced_price', 0);
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

class ProductException extends \Exception
{
}