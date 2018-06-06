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

abstract class Model extends \Kreatif\Model
{
    static $lang_fields = [];

    public function getForm($form = null, $excludedFields = [])
    {
        return parent::getForm($form, $excludedFields);
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
        } else {
            $value = $this->unprepare($value);
        }
        return $value;
    }

    public static function isRegistered($table)
    {
        $table_classes = \rex_addon::get('simpleshop')
            ->getConfig('table_classes');
        return isset($table_classes[$table]);
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
                    $data = (array)$value['data'];

                    if (class_exists($value['class'])) {
                        if (isset ($data['id'])) {
                            $_Object = call_user_func_array([$value['class'], 'get'], [$data['id']]);
                            if (is_object($_Object)) {
                                $Object = clone $_Object;
                            } else {
                                $Object = $value['class']::create();
                            }
                        }
                        if (!isset($Object)) {
                            $Object = call_user_func([$value['class'], 'create']);
                        }
                    } else {
                        $Object = Std::create();
                    }

                    foreach ($data as $name => $value) {
                        $Object->setValue($name, $value);
                    }
                    $value = $Object;
                } else if (is_array($value)) {
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
                    } else {
                        $_values[$name] = $val;
                    }
                }
                $value = json_encode($_values);
            } else if (is_object($value)) {
                $class_name = get_class($value);
                $value      = json_encode(['class' => $class_name, 'data' => $value->getData()]);
            }
        }
        return $data;
    }

    public static function ext_setValueField(\rex_extension_point $Ep)
    {
        $subject = $Ep->getSubject();

        if (!\rex::isBackend()) {
            $Object      = $Ep->getParam('object');
            $type        = $Ep->getParam('type_name');
            $table       = $Object->getTable()
                ->getTableName();
            $_excludeds  = (array)$Ep->getParam('excluded_fields');
            $fieldConfig = array_merge(FragmentConfig::getValue("yform_fields.{$table}._fieldDefaults", []), FragmentConfig::getValue("yform_fields.{$table}.{$subject[0]}", []));
            $excluded    = array_merge(FragmentConfig::getValue("yform_fields.{$table}._excludedFields", []), $_excludeds);

            if (in_array($subject[0], $excluded)) {
                $subject = false;
            } else if ($subject) {
                if ($type == 'be_manager_relation') {
                    $subject[3] = strtr($subject[3], ['_1' => '_' . \rex_clang::getCurrentId()]);
                }
                $subject = array_merge($subject, $fieldConfig);
            }
        }
        return $subject;
    }

    public static function ext_setValidateField(\rex_extension_point $Ep)
    {
        $subject = $Ep->getSubject();

        if (!\rex::isBackend()) {
            $Object = $Ep->getParam('object');
            $table  = $Object->getTable()
                ->getTableName();

            $_excludeds = (array)$Ep->getParam('excluded_fields');
            $excluded   = array_merge(FragmentConfig::getValue("yform_fields.{$table}._excludedFields", []), $_excludeds);

            if (in_array($subject[0], $excluded)) {
                $subject = false;
            }
        }
        return $subject;
    }
}