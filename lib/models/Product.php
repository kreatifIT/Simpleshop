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

    public static function filterQuery(\rex_yform_manager_query $stmt = null)
    {
        $lang_id = \rex_clang::getCurrentId();

        if (!$stmt) {
            $stmt = self::query();
        }
        $stmt->whereRaw('(price > 0 OR reduced_price > 0)')
            ->where('amount', 0, '>')
            ->where("name_{$lang_id}", '', '!=')
            ->where('status', 1);
        return $stmt;
    }

    public function getUrl($params = [], $lang_id = null)
    {
        $vkey = $this->getValue('variant_key');

        if ($vkey) {
            $params = array_merge($params, [
                'vkey' => $vkey,
            ]);
        }
        return parent::getUrl($params, $lang_id);
    }

    public function getKey()
    {
        return rtrim($this->getId() .'|'. $this->getValue('variant_key'), '|');
    }

    public function getFeatures()
    {
        if ($this->__features === null) {
            $feature_val_ids  = trim($this->getValue('features'));
            $this->__features = strlen($feature_val_ids) ? $this->_getFeatures(explode(',', $feature_val_ids)) : [];
        }
        return $this->__features;
    }

    public function hasReducedPrice()
    {
        return $this->getValue('reduced_price') > 0;
    }

    public function getPrice($includeTax = true, $use_reduced = true, $only_reduced = false)
    {
        $type     = $this->getValue('type');
        $price    = $this->getValue('price');
        $discount = $this->getValue('discount');
        $Settings = \rex::getConfig('simpleshop.Settings');

        if ($type == 'giftcard') {
            $includeTax = false;
            $extras     = $this->getValue('extras');
            $price      = $extras['price'];
        } else if ($use_reduced) {
            $reduced = $this->getValue('reduced_price');
            if($only_reduced) {
                $price   = $reduced;
            } else {
                $price   = $reduced > 0 ? $reduced : $price;
            }
        }

        $price = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Product.getPrice', $price, [
            'product'     => $this,
            'includeTax'  => $includeTax,
            'use_reduced' => $use_reduced,
        ]));

        if (is_object($discount)) {
            if ($discount->getValue('discount_value') > 0) {
                $price -= $discount->getValue('discount_value');
            } else if ($discount->getValue('discount_percent') > 0) {
                $price = $price / 100 * (100 - $discount->getValue('discount_percent'));
            }
        }

        if ($includeTax) {
            $tax = Tax::get($this->getValue('tax'))
                ->getValue('tax');

            if ($Settings['brutto_prices']) {
                $this->__tax = ($price / ($tax + 100)) * $tax;
            } else {
                $this->__tax = $price * $tax * 0.01;
                $price       = $price + $this->__tax;
            }
        } else if ($Settings['brutto_prices']) {
            $tax = Tax::get($this->getValue('tax'))
                ->getValue('tax');

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
                $_value = FeatureValue::get($feature_id);

                if ($_value) {
                    $_id                    = $_value->getValue('feature_id');
                    $feature_ids[]          = $_id;
                    $feature_values[$_id][] = $_value;
                }
            }
            // get features
            $_features = Feature::query()
                ->where('id', $feature_ids)
                ->find();

            foreach ($_features as $feature) {
                // assign feature values to the feature
                $feature->values = $feature_values[$feature->getValue('id')];
                //
                $result[$feature->getValue('key')] = $feature;
            }
        }
        return $result;
    }

    public static function getProductVariant($variant_key)
    {
        list ($product_id, $variant_key) = explode('|', $variant_key);

        $Object = self::get($product_id);

        if ($Object && strlen($variant_key)) {
            $Object = $Object->getVariant($variant_key);
        }
        return $Object;
    }

    public function getVariant($variant_key)
    {
        $product_id = $this->getValue('id');
        $stmt       = Variant::query();
        $stmt->where('product_id', $product_id);
        $stmt->where('variant_key', $variant_key);
        $stmt->where('type', 'NE', '!=');
        $variant = $stmt->findOne();

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
            $sql  = \rex_sql::factory();
            $stmt = Variant::query()
                ->resetSelect()
                ->select('id')
                ->select('variant_key')
                ->where('product_id', $this->getId())
                ->where('type', 'NE', '!=')
                ->orderBy('prio', 'asc');

            $_variants = $stmt->find();

            $this->__feature_data = [
                'variants' => [],
            ];

            foreach ($_variants as $variant) {
                $key  = $variant->getValue('variant_key');
                $_ids = array_filter(explode(',', $key));

                // order ids by feature priority
                $query = '
                    SELECT m.id 
                    FROM ' . FeatureValue::TABLE . ' AS m
                    LEFT JOIN ' . Feature::TABLE . ' AS jt1 ON jt1.id = m.feature_id
                    WHERE m.id IN(' . implode(',', $_ids) . ')
                    ORDER BY jt1.prio ASC
                ';
                $_ids  = $sql->getArray($query, [], \PDO::FETCH_COLUMN);

                if (count($_ids)) {
                    // apply default values from product
                    $clone   = clone $this;
                    $variant = $clone->getVariant($key);
                    // set variants
                    $this->__feature_data['variants'][implode(',', $_ids)] = $variant;
                }
            }
        }
        return $this->__feature_data;
    }

    public static function getProductByKey($key, $cart_quantity = null, $extras = [], $throwErrors = true)
    {
        if (!strlen($key)) {
            return false;
        }
        list ($product_id, $variant_key) = explode('|', trim($key, '|'));

        $_this = self::query()
            ->where('id', $product_id)
            ->where('status', 1)
            ->findOne();

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
            $variant = Variant::query()
                ->where('product_id', $product_id)
                ->where('type', 'NE', '!=')
                ->where('variant_key', $variant_key)
                ->findOne();

            if (!$variant) {
                // the given combination does not exist!
                throw new ProductException("The variant doesn't exist --key:{$key}", 3);
            }
            $_this->applyVariantData($variant->getData());
        }

        if ($_features && count($feature_ids)) {
            foreach ($feature_ids as $feature_id) {
                // get variants
                $feature = FeatureValue::query()
                    ->where('id', $feature_id)
                    ->where('status', 1)
                    ->findOne();

                if ($throwErrors) {
                    if (!$feature) {
                        throw new ProductException("No feature with ID = " . $feature_id . " exists --key:{$key}", 2);
                    } else if ($_this->getValue('amount') <= 0) {
                        // make some availabilty checks
                        throw new ProductException("Product not available any more --key:{$key}", 4);
                    } else if ($_this->getValue('amount') < $_this->getValue('cart_quantity')) {
                        throw new ProductException("Amount of product is lower than cart quantity --key:{$key}", 5);
                    }
                }
                $features[] = $feature;
            }
        } else {
            if ($throwErrors) {
                if ($_this->getValue('amount') <= 0) {
                    // make some availabilty checks
                    throw new ProductException("Product not available any more --key:{$key}", 4);
                } else if ($_this->getValue('amount') < $_this->getValue('cart_quantity')) {
                    throw new ProductException("Amount of product is lower than cart quantity --key:{$key}", 5);
                }
            }
        }
        $_this->features = $features;
        return $_this;
    }

    public function applyVariantData($variant_data)
    {
        foreach ($variant_data as $key => $value) {
            if (in_array($key, ['price', 'reduced_price', 'width', 'height', 'weight', 'length'])) {
                if ((float)$value > 0) {
                    $this->setValue($key, (float)$value);
                }
            } else if ($key == 'amount') {
                if ($value != '') {
                    $this->setValue($key, $value);
                }
            } else if ($value != '' && !in_array($key, ['id', 'product_id', 'type'])) {
                $this->setValue($key, $value);
            }
        }
        if ($variant_data['type'] == 'FREE') {
            // if variant is free set price to zero
            $this->setValue('price', 0);
            $this->setValue('reduced_price', 0);
        } else if ((float)$variant_data['price'] > 0 && (float)$variant_data['reduced_price'] <= 0) {
            // if variant has a price
            $this->setValue('reduced_price', 0);
        }
    }

    public function getDiscountPercentage()
    {
        $result = 0;

        if ($this->hasReducedPrice()) {
            $Z      = $this->getValue('price') - $this->getValue('reduced_price');
            $result = $Z * 100 / $this->getValue('price');
        }
        return $result;
    }

    public function generatePath($lang_id, $path = '')
    {
        $_paths   = [];
        $catId    = $this->getValue('category_id');
        $category = $catId ? Category::get($this->getValue('category_id')) : null;

        if ($category) {
            $parents = $category->getParentTree();

            foreach ($parents as $parent) {
                $_paths[] = Url::getRewriter()
                    ->normalize($parent->getValue('name_' . $lang_id));
            }
        }
        $_paths[] = $path;
        return implode('/', $_paths);
    }

    public static function ext_yform_data_delete(\rex_extension_point $Ep)
    {
        $result = $Ep->getSubject();

        if ($result !== false && $Ep->getParam('table')
                ->getTableName() == self::TABLE
        ) {
            // remove all related variants
            $obj_id = $Ep->getParam('data_id');
            $query  = "DELETE FROM " . Variant::TABLE . " WHERE product_id = {$obj_id}";
            $sql    = \rex_sql::factory();
            $sql->setQuery($query);
        }
        return $result;
    }

    public static function ext_setUrlObject(\rex_extension_point $Ep)
    {
        $Object = $Ep->getSubject();

        if ($Object && $Object->getTableName() == self::TABLE) {
            $vkey = trim(rex_get('vkey', 'string'));

            if ($vkey != '') {
                try {
                    $Object = $Object->getVariant($vkey);

                    \rex_extension::register('YREWRITE_ROBOTS_TAG', function (\rex_extension_point $Ep) {
                        return false;
                    });
                } catch (ProductException $ex) {
                    \rex_response::setStatus(404);
                    $Object = false;
                }
            } else if (!$Object->valueIsset('code')) {
                $collection = $Object->getFeatureVariants();
                $Variant    = array_shift($collection['variants']);

                if ($Variant) {
                    $Object                 = $Variant;
                    $Object->__feature_data = null;
                } else {
                    \rex_response::setStatus(404);
                    $Object = false;
                }
            }
        }
        return $Object;
    }

    public static function ext_queryCollection(\rex_extension_point $Ep)
    {
        $collection = $Ep->getSubject();

        if ($Ep->getParam('table') == self::TABLE) {
            $result = [];
            $sql    = \rex_sql::factory();

            foreach ($collection as $Object) {
                $sql->setQuery("
                    SELECT variant_key 
                    FROM " . Variant::TABLE . " 
                    WHERE product_id = :pid 
                    AND type != 'NE'
                    ORDER BY prio ASC LIMIT 1", ['pid' => $Object->getId()]);
                $vkey = @$sql->getValue('variant_key');

                if ($vkey != '') {
                    $Object = $Object->getVariant($vkey);
                }
                $result[] = $Object;
            }
            $collection = $result;
        }
        return $collection;
    }

    public static function ext_tableManagerInfo(\rex_extension_point $Ep)
    {
        $subject = $Ep->getSubject();
        $Manager = $Ep->getParam('manager');

        if ($Manager && $Manager->table->getTablename() == Product::TABLE) {
            $fragment = new \rex_fragment();
            $content  = $fragment->parse('simpleshop/backend/product_list_functions.php');

            if (strlen($content)) {
                $fragment = new \rex_fragment();
                $fragment->setVar('body', $content, false);
                $fragment->setVar('class', 'action');
                $subject = $fragment->parse('core/page/section.php');
            }
        }
        return $subject;
    }


    public static function be_toggleRexCategoryId()
    {
        $request = \rex_api_simpleshop_be_api::$inst->request;
        $Object  = self::get($request['id']);

        if ($Object) {
            $categories = $Object->getArrayValue('category');

            if ($request['action'] == 'add') {
                $categories[] = $request['cat_id'];
            } else {
                $index = array_search($request['cat_id'], $categories);

                if ($index !== false) {
                    unset($categories[$index]);
                }
            }
            $categories = array_unique($categories);

            $Object->setValue('category', implode(',', $categories));

            if (!$Object->save()) {
                \rex_api_simpleshop_be_api::$inst->success = false;
                \rex_api_simpleshop_be_api::$inst->errors  = array_merge(\rex_api_simpleshop_be_api::$inst->errors, $Object->getMessages());
            }
            \rex_api_simpleshop_be_api::$inst->response['categories'] = $categories;
        }
    }
}

class ProductException extends \Exception
{
    /*
     * Error Codes:
     * 1 = No product with ID
     * 2 = No feature with ID
     * 3 = The variant doesn't exist
     * 4 = Product not available any more
     * 5 = Amount of product is lower than cart quantity
     */
}