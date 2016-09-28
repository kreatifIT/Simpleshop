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

\rex_yform_manager_dataset::setModelClass('rex_customer', Customer::class);
\rex_yform_manager_dataset::setModelClass('rex_shop_product', Product::class);
\rex_yform_manager_dataset::setModelClass('rex_shop_category', Category::class);