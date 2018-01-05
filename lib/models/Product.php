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

class Product extends Model
{
    const TABLE        = 'rex_shop_product';
    const URL_PARAMKEY = 'product_id';

    protected $__tax          = null;
    protected $__feature_data = null;
    protected $__variants     = null;
    protected $__features     = null;


    public function getFeatures()
    {
        if ($this->__features === null) {
            $feature_val_ids  = trim($this->getValue('features'));
            $this->__features = strlen($feature_val_ids) ? $this->_getFeatures(explode(',', $feature_val_ids)) : [];
        }
        return $this->__features;
    }

    public function getPrice($includeTax = false, $use_reduced = true)
    {
        $type  = $this->getValue('type');
        $price = $this->getValue('price');

        if ($type == 'giftcard') {
            $includeTax = false;
            $extras     = $this->getValue('extras');
            $price      = $extras['price'];
        }
        else if ($use_reduced) {
            $reduced = $this->getValue('reduced_price');
            $price   = $reduced > 0 ? $reduced : $price;
        }
        if ($includeTax) {
            $tax = Tax::get($this->getValue('tax'))->getValue('tax');

            if ($this->getSetting('brutto_prices')) {
                $this->__tax = ($price / ($tax + 100)) * $tax;
            }
            else {
                $this->__tax = $price * $tax * 0.01;
                $price       = $price + $this->__tax;
            }
        }
        else if ($this->getSetting('brutto_prices')) {
            $tax = Tax::get($this->getValue('tax'))->getValue('tax');

            $this->__tax = ($price / ($tax + 100)) * $tax;
            $price       = $price - $this->__tax;
        }
        return $price;
    }

    public function getTax()
    {
        if ($this->__tax === null) {
            $this->getPrice(true);
        }
        return $this->__tax;
    }

    public function _getFeatures($feature_val_ids)
    {
        $result = [];

        if (count($feature_val_ids)) {
            $feature_ids    = [];
            $feature_values = [];
            // get feature values
            foreach ($feature_val_ids as $feature_id) {
                $_value                 = FeatureValue::get($feature_id);
                $_id                    = $_value->getValue('feature_id');
                $feature_ids[]          = $_id;
                $feature_values[$_id][] = $_value;
            }
            // get features
            $_features = Feature::query()->where('id', $feature_ids)->find();

            foreach ($_features as $feature) {
                // assign feature values to the feature
                $feature->values = $feature_values[$feature->getValue('id')];
                //
                $result[$feature->getValue('key')] = $feature;
            }
        }
        return $result;
    }

    public function getVariant($variant_key)
    {
        $product_id = $this->getValue('id');
        $variant    = Variant::query()->where('product_id', $product_id)->where('variant_key', $variant_key)->where('type', 'NE', '!=')->findOne();

        if (!$variant) {
            throw new ProductException("The variant doesn't exist --key:{$product_id}|{$variant_key}", 3);
        }
        // apply default values from variant
        $this->applyVariantData($variant->getData());
        return $this;
    }

    public function getFeatureVariants()
    {
        if ($this->__feature_data === null) {
            $_variants = Variant::query()->resetSelect()->select('id')->select('variant_key')->where('product_id', $this->getValue('id'))->where('type', 'NE', '!=')->find();

            $this->__feature_data = [
                'mapping'  => [],
                'features' => [],
                'variants' => [],
            ];

            foreach ($_variants as $variant) {
                $key  = $variant->getValue('variant_key');
                $_ids = explode(',', $key);
                // apply default values from product
                $clone   = clone $this;
                $variant = $clone->getVariant($key);
                // set the min amount number to know if available or not
                $amount = $clone->getValue('amount');
                // set variants
                $this->__feature_data['variants'][$key] = $variant;

                // create the mapping
                foreach ($_ids as $id) {
                    if (!isset($this->__feature_data['mapping'][$id]['min_amount'])) {
                        $this->__feature_data['mapping'][$id]['min_amount'] = $amount > 0 ? $amount : 0;
                    }
                    foreach ($_ids as $__id) {
                        if ($id != $__id) {
                            if ($amount > 0 && ($amount < $this->__feature_data['mapping'][$id]['min_amount'] || $this->__feature_data['mapping'][$id]['min_amount'] == 0)) {
                                $this->__feature_data['mapping'][$id]['min_amount'] = $amount;
                            }
                            $this->__feature_data['mapping'][$id]['variants'][$__id] = $key;
                        }
                    }
                }
            }
            // get all linked features
            $this->__feature_data['features'] = $this->_getFeatures(array_keys($this->__feature_data['mapping']));
        }
        return $this->__feature_data;
    }

    public function getAllVariants()
    {
        if ($this->__variants === null) {
            $_variants = Variant::query()->resetSelect()->select('id')->select('variant_key')->where('product_id', $this->getValue('id'))->where('type', 'NE', '!=')->find();

            $this->__feature_data = [
                'features' => [],
                'variants' => [],
            ];

            foreach ($_variants as $variant) {
                $key  = $variant->getValue('variant_key');
                $_ids = explode(',', $key);
                // apply default values from product
                $clone   = clone $this;
                $variant = $clone->getVariant($key);
                // set variants
                $this->__variants['variants'][$key] = $variant;

                if (parent::isRegistered(FeatureValue::TABLE)) {
                    // create the mapping
                    foreach ($_ids as $id) {
                        if (!isset ($this->__variants['features'][$id])) {
                            $this->__variants['features'][$id] = FeatureValue::get($id);
                        }
                    }
                }
            }
        }
        return $this->__variants;
    }

    public static function getProductByKey($key, $cart_quantity = null, $extras = [])
    {
        if (!strlen($key)) {
            return false;
        }
        list ($product_id, $variant_key) = explode('|', trim($key, '|'));

        $_this = self::query()->where('id', $product_id)->where('status', 1)->findOne();

        if (!$_this) {
            throw new ProductException("No product with ID = " . $product_id . " exists --key:{$key}", 1);
        }
        $features = [];
        $_this->setValue('key', $key);
        $_this->setValue('cart_quantity', $cart_quantity);
        $_this->setValue('extras', $extras);
        $_features   = $_this->getValue('features');
        $feature_ids = $variant_key ? explode(',', $variant_key) : [];

        if (count($feature_ids)) {
            $variant = Variant::query()->where('product_id', $product_id)->where('type', 'NE', '!=')->where('variant_key', $variant_key)->findOne();

            if (!$variant) {
                // the given combination does not exist!
                throw new ProductException("The variant doesn't exist --key:{$key}", 3);
            }
            $_this->applyVariantData($variant->getData());
        }

        if ($_features && count($feature_ids)) {
            foreach ($feature_ids as $feature_id) {
                // get variants
                $feature = FeatureValue::query()->where('id', $feature_id)->where('status', 1)->findOne();
                if (!$feature) {
                    throw new ProductException("No feature with ID = " . $feature_id . " exists --key:{$key}", 2);
                }
                else if ($_this->getValue('amount') <= 0) {
                    // make some availabilty checks
                    throw new ProductException("Product not available any more --key:{$key}", 4);
                }
                else if ($_this->getValue('inventory') == 'F' && $_this->getValue('amount') < $_this->getValue('cart_quantity')) {
                    throw new ProductException("Amount of product is lower than cart quantity --key:{$key}", 5);
                }
                $features[] = $feature;
            }
        }
        else {
            if ($_this->getValue('amount') <= 0) {
                // make some availabilty checks
                throw new ProductException("Product not available any more --key:{$key}", 4);
            }
            else if ($_this->getValue('inventory') == 'F' && $_this->getValue('amount') < $_this->getValue('cart_quantity')) {
                throw new ProductException("Amount of product is lower than cart quantity --key:{$key}", 5);
            }
        }
        $_this->features = $features;
        return $_this;
    }

    public function applyVariantData($variant_data)
    {
        foreach ($variant_data as $key => $value) {
            if (in_array($key, ['price', 'reduced_price', 'width', 'height', 'weight', 'length'])) {
                if ((float) $value > 0) {
                    $this->setValue($key, (float) $value);
                }
            }
            elseif ($key == 'amount') {
                //                if ($this->getValue('inventory') == 'F' && $value != '')
                if ($value != '') {
                    $this->setValue($key, $value);
                }
            }
            elseif ($value != '' && !in_array($key, ['id', 'product_id', 'type'])) {
                $this->setValue($key, $value);
            }
        }
        if ($variant_data['type'] == 'FREE') {
            // if variant is free set price to zero
            $this->setValue('price', 0);
            $this->setValue('reduced_price', 0);
        }
        else if ((float) $variant_data['price'] > 0 && (float) $variant_data['reduced_price'] <= 0) {
            // if variant has a price
            $this->setValue('reduced_price', 0);
        }
    }

    public function generatePath($lang_id, $path = '')
    {
        $_paths   = [];
        $catId    = $this->getValue('category_id');
        $category = $catId ? Category::get($this->getValue('category_id')) : null;

        if ($category) {
            $parents = $category->getParentTree();

            foreach ($parents as $parent) {
                $_paths[] = Url::getRewriter()->normalize($parent->getValue('name_' . $lang_id));
            }
        }
        $_paths[] = $path;
        return implode('/', $_paths);
    }

    public static function ext_yform_data_delete($params)
    {
        $result = $params->getSubject();

        if ($result !== false && $params->getParam('table')->getTableName() == self::TABLE) {
            // remove all related variants
            $obj_id = $params->getParam('data_id');
            $query  = "DELETE FROM " . Variant::TABLE . " WHERE product_id = {$obj_id}";
            $sql    = \rex_sql::factory();
            $sql->setQuery($query);
        }
        return $result;
    }
}

class ProductException extends \Exception
{
}