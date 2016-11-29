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

//if (!file_exists(rex_path::addonData($this->getPackageId(), 'settings.yml')))
//{
//    rex_dir::copy($this->getPath('data'), $this->getDataPath());
//}

rex_dir::create(rex_path::addonData('simpleshop', 'log'), TRUE);