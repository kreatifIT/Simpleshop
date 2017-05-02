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

use Symfony\Component\Yaml\Exception\RuntimeException;

abstract class Plugin
{
    protected static $classes = [];
    protected static $plugins = [];

    public static function register($class, $plugin_name)
    {
        self::$classes[static::$type][$class]       = ['plugin_name' => $plugin_name, 'this' => NULL];
        self::$plugins[static::$type][$plugin_name] = $class;
    }

    public static function get($plugin_name)
    {
        return self::getByClass(self::$plugins[static::$type][$plugin_name]);
    }

    public static function getByClass($class)
    {
        if (!array_key_exists($class, self::$classes[static::$type]))
        {
            throw new RuntimeException("'{$class}' is not as registered ". static::$type ." class");
        }
        if (self::$classes[static::$type][$class]['this'] === NULL)
        {
            $_this = new $class();
            // check if is extended from abstract
            if ($_this instanceof PluginAbstract)
            {
                $_this->plugin_name = self::$classes[static::$type][$class]['plugin_name'];
                self::$classes[static::$type][$class]['this'] = $_this;
            }
            else
            {
                throw new RuntimeException("'{$class}' must be instance of PluginAbstract");
            }
        }
        return self::$classes[static::$type][$class]['this'];
    }

    public static function getAll()
    {
        $result = [];
        foreach (self::$classes[static::$type] as $class => $data)
        {
            $result[] = self::getByClass($class);
        }
        return $result;
    }
}


abstract class PluginAbstract
{
    public $plugin_name;

    public abstract static function get();
    public abstract function getName();

    public static function create()
    {
        return static::get();
    }

    public function getValue($key)
    {
        $value = NULL;

        if (property_exists($this, $key))
        {
            $value = Model::unprepare($this->$key);
        }
        return $value;
    }

    public function setValue($key, $value)
    {
        $this->$key = $value;
    }

    public function getPluginName()
    {
        return $this->plugin_name;
    }

    public function getData()
    {
        return get_object_vars($this);
    }
}