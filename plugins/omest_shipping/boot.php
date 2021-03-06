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

/*
 * Documentation: http://wiki.olc.omest.com/doku.php
 * User: omest_wiki
 * Password: rai6bwmWxJa0uQMflUmU
 */



Shipping::register(Omest::class, 'omest_shipping');

\rex_extension::register('simpleshop.Order.getShippingKey', ['\FriendsOfREDAXO\Simpleshop\Omest', 'ext__getShippingKey']);