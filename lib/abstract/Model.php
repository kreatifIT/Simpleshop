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

    public static function isRegistered($table)
    {
        $table_classes = \rex_addon::get('simpleshop')->getConfig('table_classes');
        return isset($table_classes[$table]);
    }

    public function getFields()
    {
        $fields  = [];
        $_fields = parent::getFields();

        foreach ($_fields as $field)
        {
            $typen = $field->getTypeName();
            $type  = $field->getType();
            $name  = $field->getName();
            $key   = $name . '_' . $type . '_' . $typen;


            if (!in_array($name, $this->excluded_fields))
            {
                if (!\rex::isBackend())
                {
                    $field['notice'] = '';
                }
                if (isset ($this->field_data[$key]))
                {
                    foreach ($this->field_data[$key] as $value_name => $value)
                    {
                        $field[$value_name] = $value;
                    }
                }
                $fields[] = $field;
            }
        }
        // add additional fields
        foreach ($this->adddional_fields as $field)
        {
            $_nfield = new \rex_yform_manager_field($field['values']);

            if ($field['values'] !== NULL)
            {
                array_splice($fields, $field['position'], 0, [$_nfield]);
            }
            else
            {
                $fields[] = $_nfield;
            }
        }
        return $fields;
    }

    public function addExcludedField($fieldnames)
    {
        if (!is_array($fieldnames))
        {
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


    public function getValue($key, $process = TRUE)
    {
        $value = parent::getValue($key);

        if ($process)
        {
            if (is_array($value))
            {
                $_values = [];
                foreach ($value as $name => $val)
                {
                    $_values[$name] = $this->unprepare($val);
                }
                $value = $_values;
            }
            else
            {
                $value = $this->unprepare($value);
            }
        }
        return $value;
    }

    public function save($prepare = FALSE)
    {
        if ($prepare)
        {
            $data = $this->prepare($this);

            foreach ($data as $name => $value)
            {
                $this->setValue($name, $value);
            }
        }
        return parent::save();
    }

    public static function unprepare($value)
    {
        if (is_string($value))
        {
            $decoded_json = json_decode($value, TRUE);

            if (json_last_error() == JSON_ERROR_NONE)
            {
                $value = $decoded_json;

                if (isset ($value['class']))
                {
                    $data = (array) $value['data'];

                    if (class_exists($value['class']))
                    {
                        if (isset ($data['id']))
                        {
                            $Object = call_user_func_array([$value['class'], 'get'], [$data['id']]);
                        }
                        if (!$Object)
                        {
                            $Object = call_user_func([$value['class'], 'create']);
                        }
                    }
                    else
                    {
                        $Object = Std::create();
                    }

                    foreach ($data as $name => $value)
                    {
                        $Object->setValue($name, $value);
                    }
                    $value = $Object;
                }
                else if (is_array($value))
                {
                    foreach ($value as $name => &$val)
                    {
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

        foreach ($data as $name => &$value)
        {
            if (is_array($value))
            {
                $_values = [];
                foreach ($value as $name => $val)
                {
                    if (is_object($val))
                    {
                        $class_name     = get_class($val);
                        $_values[$name] = json_encode(['class' => $class_name, 'data' => self::prepare($val)]);
                    }
                    else
                    {
                        $_values[$name] = $val;
                    }
                }
                $value = json_encode($_values);
            }
            else if (is_object($value))
            {
                $class_name = get_class($value);
                $value      = json_encode(['class' => $class_name, 'data' => $value->getData()]);
            }
        }
        return $data;
    }
}