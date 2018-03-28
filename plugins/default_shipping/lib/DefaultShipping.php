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

class DefaultShipping extends ShippingAbstract
{
    const NAME = 'simpleshop.shipping_default';

    protected $tax_percentage = 22;

    public function getName()
    {
        if ($this->name == '') {
            $this->name = Wildcard::get(self::NAME);
        }
        return parent::getName();
    }

    protected function calculatePrice($Order)
    {
        if ($this->price == 0) {
            $Address = $Order->getShippingAddress();
            $Country = $Address ? Country::get($Address->getValue('country')) : null;

            if ($Country && $Country->valueIsset('shipping_costs')) {
                $this->price = $Country->getValue('shipping_costs');
            }
        }
    }

    public function getPrice($Order, $products = null)
    {
        $this->calculatePrice($Order);
        return parent::getPrice($Order, $products);
    }

    public function getNetPrice($Order, $products = null)
    {
        $this->calculatePrice($Order);
        return parent::getNetPrice($order, $products);
    }
}