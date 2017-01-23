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

\rex_extension::register('PACKAGES_INCLUDED', function ()
{
    if ($this->getProperty('compile'))
    {
        $compiler   = new \rex_scss_compiler();
        $scss_files = \rex_extension::registerPoint(new \rex_extension_point('BE_STYLE_SCSS_FILES', [$this->getPath('assets/styles.scss')]));
        $compiler->setScssFile($scss_files);
        $compiler->setCssFile($this->getPath('assets/styles.css'));
        $compiler->compile();
        \rex_file::copy($this->getPath('assets/styles.css'), $this->getAssetsPath('css/styles.css'));
    }
});
// CSS
\rex_view::addCssFile($this->getAssetsUrl('css/styles.css?mtime='. filemtime($this->getAssetsPath('css/styles.css'))));