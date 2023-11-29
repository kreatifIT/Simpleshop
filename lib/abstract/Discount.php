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
		$discount    = 0;
		$addDiscount = true;

		$addDiscount = (bool)\rex_extension::registerPoint(
			new \rex_extension_point('simpleshop.coupon.addDiscountCheck', $addDiscount, [
				'Order'  => $Order,
				'Coupon' => $this,
			])
		);

		if ($addDiscount) {
			$discountGrossPrice = (array)\rex_extension::registerPoint(
				new \rex_extension_point('simpleshop.coupon.apply', $gross_prices, [
					'Order'  => $Order,
					'Coupon' => $this,
				])
			);

			if ($name == 'manual_discount') {
				$discount = $this->getValue('discount_value');
			} elseif ($this->getValue('free_shipping')) {
				$Order->setValue('shipping_costs', 0);
			} elseif ($this->getValue('discount_value') > 0) {
				$discount = $this->getValue('discount_value');
			} elseif ($this->getValue('discount_percent') > 0) {
				$discount = array_sum($discountGrossPrice) / 100 * $this->getValue('discount_percent');
			}

			if ($discount > 0 && $addDiscount) {
				$discount = $discount - $this->applyToGrossPrices($discount, $gross_prices);
			}
			$this->setValue('value', $discount);

			return $discount;
		}
	}

	public function applyToCart(&$brut_prices)
	{
		$discount    = 0;
		$addDiscount = true;

		$addDiscount = (bool)\rex_extension::registerPoint(
			new \rex_extension_point('simpleshop.coupon.addDiscountCheck', $addDiscount, [
				'Coupon' => $this,
			])
		);
		if ($addDiscount) {
			$discountBrutPrice = (array)\rex_extension::registerPoint(
				new \rex_extension_point('simpleshop.coupon.apply', $brut_prices, [
					'Coupon' => $this,
				])
			);
			if ($this->getValue('free_shipping')) {
				// nothing to do?
				$discount = 0;
			} elseif ($this->getValue('discount_value') > 0) {
				$discount = $this->getValue('discount_value');
			} elseif ($this->getValue('discount_percent') > 0) {
				$discount = array_sum($discountBrutPrice) / 100 * $this->getValue('discount_percent');
			}

			if ($discount > 0 && $addDiscount) {
				$discount = $discount - $this->applyToGrossPrices($discount, $brut_prices);
			}
			$this->setValue('value', $discount);

			return $discount;
		}
	}

	protected function applyToGrossPrices($_discount, &$gross_prices)
	{
		// sort by tax percent
		krsort($gross_prices);

		foreach ($gross_prices as $tax => &$gross_price) {
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