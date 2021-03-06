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

class Std extends PluginAbstract
{
    public static function get()
    {
        return new self();
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getData()
    {
        return get_object_vars($this);
    }

    public static function ext_unprepareNEObject(\rex_extension_point $ep) {
        $ep->setSubject(self::create());
    }
}

