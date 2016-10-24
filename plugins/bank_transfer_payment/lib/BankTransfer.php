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

class BankTransfer extends PaymentAbstract
{
    public function getPrice()
    {
        return 0;
    }

    public function getName()
    {
        return '###shop.bank_transfer###';
    }

    public function getPaymentInfo()
    {
        return '###shop.bank_transfer_payment_info###';
    }
}