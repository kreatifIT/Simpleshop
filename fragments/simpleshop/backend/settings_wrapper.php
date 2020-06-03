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

$_FUNC  = rex_post('func', 'string');
$title  = $this->getVar('title', 'Simpleshop');
$key    = $this->getVar('key');
$path   = $this->getVar('fragment_path');
$config = \rex::getConfig($key, []);

echo \rex_view::title($title);

if ($_FUNC == 'save') {
    unset($_POST['func']);
    $toUnset = array_diff_key($config, $_POST);

    foreach ($toUnset as $_key => $_value) {
        unset($config[$_key]);
    }
    $config = array_merge($config, $_POST);
    \rex::setConfig($key, $config);
    echo \rex_view::info(\rex_i18n::msg('label.data_saved'));
}

$this->setVar('Settings', $config);
ob_start();
$this->subfragment($path);
$content = ob_get_clean();

$fragment = new \rex_fragment();
$fragment->setVar('body', $content, false);
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', \rex_i18n::msg('setup'), false);
$sections = $fragment->parse('core/page/section.php');

$formElements = [
    ['field' => '<a class="btn btn-abort" href="' . \rex_url::currentBackendPage() . '">' . \rex_i18n::msg('form_abort') . '</a>'],
    ['field' => '<button class="btn btn-apply rex-form-aligned" type="submit" name="func" value="save"' . \rex::getAccesskey(\rex_i18n::msg('update'), 'apply') . '>' . \rex_i18n::msg('update') . '</button>'],
];
$fragment     = new \rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new \rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('buttons', $buttons, false);
$sections .= $fragment->parse('core/page/section.php');

echo '<form action="' . \rex_url::currentBackendPage() . '" method="post">' . $sections . '</form>';