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
	public function applyToOrder($Order, &$gross_prices, $name = '')
	{
		if (!$Order) {
			return false;
		}
		$discount = 0;

		if ($name == 'manual_discount') {
			$discount = $this->getValue('discount_value');
		} elseif ($this->getValue('free_shipping')) {
			$Order->setValue('shipping_costs', 0);
		} elseif ($this->getValue('discount_value') > 0) {
			$discount = $this->getValue('discount_value');
		} elseif ($this->getValue('discount_percent') > 0) {
			$discount = array_sum($gross_prices) / 100 * $this->getValue('discount_percent');
		}

		$discount = \rex_extension::registerPoint(
			new \rex_extension_point('simpleshop.coupon.discount', $discount, [
				'brut_prices' => &$gross_prices,
				'discount'    => $this,
			])
		);

		if ($discount > 0) {
			$discount = $discount - $this->applyToGrossPrices($discount, $gross_prices);
		}
		$this->setValue('value', $discount);

		return $discount;
	}

	public function applyToCart(&$brut_prices)
	{
		$discount = 0;
		if ($this->getValue('free_shipping')) {
			// nothing to do?
			$discount = 0;
		} elseif ($this->getValue('discount_value') > 0) {
			$discount = $this->getValue('discount_value');
		} elseif ($this->getValue('discount_percent') > 0) {
			$discount = array_sum($brut_prices) / 100 * $this->getValue('discount_percent');
		}
		
		$discount = \rex_extension::registerPoint(
			new \rex_extension_point('simpleshop.coupon.discount', $discount, [
				'brut_prices' => &$brut_prices,
				'discount'    => $this,
			])
		);

		if ($discount > 0) {
			$discount = $discount - $this->applyToGrossPrices($discount, $brut_prices);
		}
		$this->setValue('value', $discount);

		return $discount;
	}

	protected function applyToGrossPrices($_discount, &$gross_prices)
	{
		// sort by tax percent
		krsort($gross_prices);

		foreach ($gross_prices as $tax => &$gross_price) {
			$_discount   = $_discount + ($_discount * $tax / 100);
			$gross_price = $this->calcPriceAndDiff($gross_price, $_discount);

			if ($_discount <= 0) {
				break;
			}
		}
		return $_discount;
	}

	protected function calcPriceAndDiff($price, &$diff)
	{
		if ($price < $diff) {
			$diff  = $diff - $price;
			$price = 0;
		} else {
			$price -= $diff;
			$diff  = 0;
		}
		return $price;
	}
}