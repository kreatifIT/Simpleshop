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

class Parcel extends Std
{
    protected $length  = null;
    protected $width   = null;
    protected $height  = null;
    protected $weight  = null;
    protected $pallett = null;

    public function __construct($length = null, $width = null, $height = null, $weight = null, $pallett = false)
    {
        $this->length  = $length;
        $this->width   = $width;
        $this->height  = $height;
        $this->weight  = $weight;
        $this->pallett = $pallett;
        return $this;
    }

    public static function get()
    {
        return new self();
    }
}