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
        if (is_string($value) && substr($value, 0, 10) == '{"class":"')
        {
            $_data  = json_decode($value);
            $Object = call_user_func([$_data->class, 'create']);
            $data   = (array) $_data->data;

            foreach ($data as $name => $value)
            {
                $Object->setValue($name, $value);
            }
            $value = $Object;
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
                $value = $_values;
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