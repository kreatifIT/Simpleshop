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

\rex::setProperty('simpleshop.product_variants', 'variants');

\rex_extension::register('PACKAGES_INCLUDED', function () {
    if ($this->getProperty('compile') || \rex_addon::get('project')->getProperty('compile') || !file_exists($this->getAssetsPath('css/backend.css'))) {
        $compiler = new \rex_scss_compiler();
        $compiler->setScssFile([$this->getPath('assets/styles.scss')]);
        $compiler->setCssFile($this->getAssetsPath('css/styles.css'));
        $compiler->compile();

        \rex_file::copy($this->getPath('assets/simpleshop.js'), $this->getAssetsPath('js/simpleshop.js'));
    }

    // include assets
    \rex_view::addCssFile($this->getAssetsUrl('css/styles.css?mtime=' . filemtime($this->getAssetsPath('css/styles.css'))));
    \rex_view::addJsFile($this->getAssetsUrl('js/simpleshop.js?mtime=' . filemtime($this->getAssetsPath('js/simpleshop.js'))));
});
