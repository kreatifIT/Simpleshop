<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

abstract class Controller
{
    protected $output        = '';
    protected $errors        = [];
    protected $params        = [];
    protected $fragment_path = [];
    protected $fragment      = null;
    protected $settings      = [];

    protected abstract function _execute();

    public function __construct()
    {
        if ($this->fragment === null) {
            $this->fragment = new \rex_fragment();
        }
    }

    public static function execute($params = [])
    {
        $name = get_called_class();
        $inst = new $name();

        $inst->params   = $params;
        $inst->settings = \rex::getConfig('simpleshop.Settings');

        foreach ($inst->params as $key => $value) {
            $inst->setVar($key, $value, false);
        }
        $inst->_execute();
        return $inst;
    }

    protected function verifyParams($required_params)
    {
        // verifiy params
        foreach ($required_params as $param) {
            if (!$this->params[$param]) {
                throw new \ErrorException("The param '{$param}' is missing!");
            }
        }
    }

    public function setVar($key, $value, $escape = true)
    {
        if ($this->fragment === null) {
            $this->fragment = new \rex_fragment();
        }
        $this->fragment->setVar($key, $value, $escape);
    }

    public function getVar($key, $default = null)
    {
        return $this->fragment->getVar($key, $default);
    }

    public function parse($fragment_path = null, $delete_whitespaces = true)
    {
        if (count($this->errors)) {
            ob_start();
            foreach ($this->errors as $error): ?>
                <div class="callout alert">
                    <p><?= isset($error['replace']) ? strtr($error['label'], ['{{replace}}' => $error['replace']]) : $error['label'] ?></p>
                </div>
            <?php endforeach;
            $this->output = ob_get_clean() . $this->output;
        }

        if ($fragment_path) {
            array_pop($this->fragment_path);
            $this->fragment_path[] = $fragment_path;
        }

        foreach ($this->fragment_path as $path) {
            $this->output .= $this->fragment->parse($path, $delete_whitespaces);
        }
        return $this->output;
    }
}