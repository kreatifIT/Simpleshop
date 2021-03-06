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


\rex_response::sendCacheControl();
\rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
rex_redirect(null, null, ['action' => 'complete', 'ts' => time()]);