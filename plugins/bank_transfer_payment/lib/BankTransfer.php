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

class BankTransfer extends PaymentAbstract
{
    const NAME = 'label.bank_transfer';

    public function getName()
    {
        if ($this->name == '')
        {
            $this->name = Wildcard::get(self::NAME);
        }
        return parent::getName();
    }
}