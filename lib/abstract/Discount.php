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

abstract class Discount extends Model
{
    public function applyToOrder($Order)
    {
        if (!$Order)
        {
            return FALSE;
        }
        if ($this->getValue('free_shipping'))
        {
            $Order->setValue('shipping_costs', 0);
        }
        else if ($this->getValue('discount_value') > 0)
        {
            $_discount = $this->getValue('discount_value');
            $Order->setValue('total', $Order->getValue('total') - $_discount);
            $this->setValue('discount', $_discount);
        }
        else if ($this->getValue('discount_percent') > 0)
        {
            $subtotal  = $Order->getValue('subtotal');
            $_discount = $subtotal / 100 * $this->getValue('discount_percent');
            $Order->setValue('total', $Order->getValue('total') - $_discount);
            $this->setValue('discount', $_discount);
        }
        $Order->setValue('discount', $Order->getValue('discount') + $_discount);

        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.Discount.applyToOrder', $Order, [
            'promotion' => $this
        ]));

        return $this;
    }
}