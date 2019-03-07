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
        $Address  = $Order->getShippingAddress();
        $Country  = $Address ? Country::get($Address->getValue('country')) : null;
        $Settings = \rex::getConfig('simpleshop.DefaultShipping.Settings');


        if ($Country && isset($Settings['costs'][$Country->getId()])) {
            $total = Session::getTotal();
            $cost  = 0;

            foreach ($Settings['costs'][$Country->getId()] as $value => $_cost) {
                if ($total >= $value) {
                    $cost = $_cost;
                    break;
                }
            }
            $this->price = (float)$cost;
        } else {
            $this->price = (float)($Settings['general_costs'] ?: 0);
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