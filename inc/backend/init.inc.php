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

\rex_extension::register('PACKAGES_INCLUDED', function () {
    $mtime = '';

    if ($this->getProperty('compile') || !file_exists($this->getAssetsPath('css/backend.css'))) {
        $mtime       = '?mtime=' . time();
        $compiler = new \rex_scss_compiler();
        $compiler->setScssFile([$this->getPath('assets/scss/backend.scss')]);
        $compiler->setCssFile($this->getAssetsPath('css/backend.css'));
        $compiler->compile();

        copy($this->getPath('assets/backend.js'), $this->getAssetsPath('backend.js'));
    }

    $stmt = FeatureValue::query();
    $stmt->where('status', 1);
    $featureVal = $stmt->findOne();

    FragmentConfig::$data['has_variants'] = (bool)$featureVal;

    \rex_view::addCssFile($this->getAssetsUrl('css/backend.css' . $mtime));
    \rex_view::addJsFile($this->getAssetsUrl('backend.js' . $mtime));
});
