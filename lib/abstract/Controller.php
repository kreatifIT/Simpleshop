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
    protected $params        = [];
    protected $fragment_path = '';
    protected $fragment      = null;

    protected abstract function _execute();


    public static function execute($params = [])
    {
        $name = get_called_class();
        $inst = new $name();

        $inst->params = $params;

        foreach ($inst->params as $key => $value) {
            $inst->setVar($key, $value);
        }
        $inst->_execute();
        return $inst;
    }

    protected function verifyParams($required_params)
    {
        // verifiy params
        foreach ($required_params as $param) {
            if (!$this->params[$param]) {
                throw new ErrorException("The param '{$param}' is missing!");
            }
        }
    }

    public function setVar($key, $value)
    {
        if ($this->fragment === null) {
            $this->fragment = new \rex_fragment();
        }
        $this->fragment->setVar($key, $value);
    }
    
    public function getVar($key, $default = null)
    {
        return $this->fragment->getVar($key, $default);
    }

    public function parse($fragment_path = null, $delete_whitespaces = true)
    {
        return $this->fragment->parse($fragment_path ?: $this->fragment_path, $delete_whitespaces);
    }
}