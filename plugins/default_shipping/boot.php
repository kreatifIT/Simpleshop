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

Shipping::register(DefaultShipping::class, 'default_shipping');

\rex_extension::register('simpleshop.Order.applyDiscounts', ['\FriendsOfREDAXO\Simpleshop\DefaultShipping', 'ext_applyDiscounts']);
\rex_extension::register('simpleshop.Order.getUpsellingPromotion', ['\FriendsOfREDAXO\Simpleshop\DefaultShipping', 'ext_getUpsellingPromotion']);