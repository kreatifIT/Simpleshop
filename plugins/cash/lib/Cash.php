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

use Sprog\Wildcard;

class Cash extends PaymentAbstract
{
    const NAME = 'shop.cash';

    public function getName()
    {
        if ($this->name == '')
        {
            $this->name = checkstr(Wildcard::get(self::NAME), self::NAME);
        }
        return parent::getName();
    }
}