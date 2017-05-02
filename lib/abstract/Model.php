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

abstract class Model extends \rex_yform_manager_dataset
{
    protected $adddional_fields = [];
    protected $excluded_fields  = [];
    protected $field_data       = [];
    static    $lang_fields      = [];
    static    $settings         = [];

    protected static function getSetting($key, $default = null)
    {
        if (count(self::$settings) == 0) {
            self::$settings = \rex::getConfig('simpleshop.Settings');
        }
        return self::$settings[$key] ?: $default;
    }

    protected static function prepareQuery($ignoreOffline = true, $params = [], $debug = 0)
    {
        $params = array_merge([
            'orderBy' => 'prio',
            'order'   => 'asc',
            'groupBy' => '',
            'limit'   => 0,
            'offset'  => 0,
            'select'  => [],
            'filter'  => [],
            'joins'   => [],
            'lang_id' => null,
        ], $params);

        $stmt = self::query(static::TABLE)->alias('m')->orderByRaw($params['orderBy'], $params['order']);

        if ($params['offset'] && $params['limit']) {
            $stmt->limit($params['offset'], $params['limit']);
        }
        else if ($params['limit']) {
            $stmt->limit($params['limit']);
        }

        if (count($params['filter'])) {
            foreach ($params['filter'] as $filter) {
                if (isset($filter[2])) {
                    $stmt->where($filter[0], $filter[1], $filter[2]);
                }
                else {
                    $stmt->where($filter[0], $filter[1]);
                }
            }
        }

        if (count($params['joins'])) {
            foreach ($params['joins'] as $join) {
                $stmt->joinRaw(($join[3] ?: ''), $join[0], $join[1], $join[2]);
            }
        }

        if (strlen($params['groupBy'])) {
            $stmt->groupByRaw($params['groupBy']);
        }

        if (count($params['select'])) {
            $stmt->selectRaw(implode(', ', (array) $params['select']));
        }

        $query = $stmt->getQuery();

        if (count($params['having'])) {
            $query = strtr($query, ['ORDER BY' => ' HAVING ' . implode(' AND ', $params['having']) . ' ORDER BY']);
        }

        if ($debug) {
            pr($query);

            if ($debug > 1) {
                pr($stmt->getParams(), 'blue');
            }
            if ($debug > 3) {
                exit;
            }
        }

        $coll = \rex_yform_manager_dataset::queryCollection($query, $stmt->getParams(), $stmt->getTableName());

        if ($debug > 2) {
            pr($coll, 'green');
        }

        if ($ignoreOffline) {
            $data    = [];
            $lang_id = $params['lang_id'] ?: \rex_clang::getCurrentId();

            foreach ($coll as $row) {
                if ($row->isOnline($lang_id)) {
                    $data[] = $row;
                }
            }
            $coll = new \rex_yform_manager_collection(static::TABLE, $data);
        }
        return $coll;
    }

    public static function getAll($ignoreOffline = true, $params = [], $debug = 0)
    {
        return self::prepareQuery($ignoreOffline, $params, $debug);
    }

    public static function getCount($ignoreOffline = true, $params = [], $debug = 0)
    {
        $params['limit']  = 0;
        $params['offset'] = 0;
        return count(self::prepareQuery($ignoreOffline, $params, $debug));
    }

    public function getValue($key, $lang_id = false, $default = '')
    {
        if ($lang_id) {
            $key .= '_' . ($lang_id === true ? \rex_clang::getCurrentId() : $lang_id);

            if (!isset(static::$lang_fields[$key])) {
                static::$lang_fields[$key] = $key;
            }
            $key = static::$lang_fields[$key];
        }
        $value = parent::getValue($key);
        $value = !is_string($value) || strlen($value) ? $value : ($value === null ? null : $default);

        if (is_array($value)) {
            $_values = [];
            foreach ($value as $name => $val) {
                $_values[$name] = $this->unprepare($val);
            }
            $value = $_values;
        }
        else {
            $value = $this->unprepare($value);
        }
        return $value;
    }

    public function valueIsset($key, $dependsOnLang = false)
    {
        $value = $this->getValue($key, $dependsOnLang);
        return is_object($value) || strlen($value) > 0;
    }

    public function getArrayValue($key, $dependsOnLang = false, $default = [])
    {
        $result = $default;
        $value  = $this->getValue($key, $dependsOnLang);

        if (strlen($value)) {

            $decoded_json = (array) json_decode($value, true);

            if (json_last_error() == JSON_ERROR_NONE) {
                $result = $decoded_json;
            }
            else {
                $result = explode(',', $value);
            }
        }
        return $result;
    }

    public function isOnline($lang_id = null)
    {
        $is_online = $this->getValue('status') === null || $this->getValue('status') == 1;
        return $is_online && ($lang_id == null || !$this->nameIsEmpty($lang_id));
    }

    /*
     * @$lang_id = [int|boolean]
     */
    public function getName($lang_id = true)
    {
        return $this->getValue('name', $lang_id) ?: $this->getValue('title', $lang_id) ?: $this->getValue('name') ?: $this->getValue('title');
    }

    public function nameIsEmpty($lang_id = null)
    {
        return strlen(trim(strip_tags($this->getName($lang_id)))) == 0;
    }

    public function getUrl($params = [])
    {
        $_params = [];

        if ($this->getValue('url_data') || defined('static::URL_PARAMKEY')) {
            $key     = defined('static::URL_PARAMKEY') ? static::URL_PARAMKEY : $this->url_data->urlParamKey;
            $_params = [$key => $this->getId()];
        }
        return rex_getUrl(null, null, array_merge($_params, $params));
    }

    public static function isRegistered($table)
    {
        $table_classes = \rex_addon::get('simpleshop')->getConfig('table_classes');
        return isset($table_classes[$table]);
    }

    public function getFields(array $filter = [])
    {
        $fields  = [];
        $_fields = parent::getFields($filter);

        foreach ($_fields as $field) {
            $typen = $field->getTypeName();
            $type  = $field->getType();
            $name  = $field->getName();
            $key   = $name . '_' . $type . '_' . $typen;


            if (!in_array($name, $this->excluded_fields)) {
                if (!\rex::isBackend()) {
                    $field['notice'] = '';
                }
                if (isset ($this->field_data[$key])) {
                    foreach ($this->field_data[$key] as $value_name => $value) {
                        $field[$value_name] = $value;
                    }
                }
                $fields[] = $field;
            }
        }
        // add additional fields
        foreach ($this->adddional_fields as $field) {
            $_nfield = new \rex_yform_manager_field($field['values']);

            if ($field['values'] !== null) {
                array_splice($fields, $field['position'], 0, [$_nfield]);
            }
            else {
                $fields[] = $_nfield;
            }
        }
        return $fields;
    }

    public function addExcludedField($fieldnames)
    {
        if (!is_array($fieldnames)) {
            $fieldnames = [$fieldnames];
        }
        $this->excluded_fields = array_unique(array_merge($this->excluded_fields, $fieldnames));
    }

    public function setFieldData($name, $data)
    {
        $this->field_data[$name] = $data;
    }

    public function addAdditionalField($type, $field_type, $position, $values)
    {
        $values = array_merge([
            'type_id'   => $type,
            'type_name' => $field_type,
        ], $values);

        $this->adddional_fields[] = [
            'values'   => $values,
            'position' => $position,
        ];
    }

    public function save($prepare = false)
    {
        if ($prepare) {
            $this->prepareData($this);
        }
        return parent::save();
    }

    public static function prepareData($Object)
    {
        $data = self::prepare($Object);

        foreach ($data as $name => $value) {
            $Object->setValue($name, $value);
        }
        return $Object;
    }

    public static function unprepare($value)
    {
        if (is_string($value)) {
            $decoded_json = json_decode($value, true);

            if (json_last_error() == JSON_ERROR_NONE) {
                $value = $decoded_json;

                if (isset ($value['class'])) {
                    $data = (array) $value['data'];

                    if (class_exists($value['class'])) {
                        if (isset ($data['id'])) {
                            $Object = call_user_func_array([$value['class'], 'get'], [$data['id']]);
                        }
                        if (!$Object) {
                            $Object = call_user_func([$value['class'], 'create']);
                        }
                    }
                    else {
                        $Object = Std::create();
                    }

                    foreach ($data as $name => $value) {
                        $Object->setValue($name, $value);
                    }
                    $value = $Object;
                }
                else if (is_array($value)) {
                    foreach ($value as $name => &$val) {
                        $val = self::unprepare($val);
                    }
                }
            }
        }
        return $value;
    }

    public static function prepare($object)
    {
        $data = $object->getData();

        foreach ($data as $name => &$value) {
            if (is_array($value)) {
                $_values = [];
                foreach ($value as $name => $val) {
                    if (is_object($val)) {
                        $class_name     = get_class($val);
                        $_values[$name] = json_encode(['class' => $class_name, 'data' => self::prepare($val)]);
                    }
                    else {
                        $_values[$name] = $val;
                    }
                }
                $value = json_encode($_values);
            }
            else if (is_object($value)) {
                $class_name = get_class($value);
                $value      = json_encode(['class' => $class_name, 'data' => $value->getData()]);
            }
        }
        return $data;
    }
}