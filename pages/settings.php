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

$sql   = \rex_sql::factory();
$_FUNC = rex_request('func', 'string');
pr($_FUNC);

$sections = '';
//$content  = '
//        <h3>' . $this->i18n('settings') . '</h3>
//        <p>' . $this->i18n('setup_column_creating_text') . '</p>
//        <div class="row">
//            <div class="col-sm-12">
//                <div class="rex-select-style">
//                    ' . \rex_var_linklist::getWidget(1, 'test', '') . '
//                </div>
//            </div>
//        </div>
//';
$content  = '
    <fieldset>
        <legend>' . $this->i18n('url_settings') . '</legend>
        <p>' . $this->i18n('setup_column_creating_text') . '</p>
        <div class="row">
            <div class="col-sm-12">
                <div class="rex-select-style">
                    ' . \rex_var_linklist::getWidget(1, 'test', '') . '
                </div>
            </div>
        </div>
    </fieldset>
';
$fragment = new \rex_fragment();
$fragment->setVar('body', $content, FALSE);
$fragment->setVar('class', 'edit', FALSE);
$fragment->setVar('title', $this->i18n('setup'), FALSE);
$sections .= $fragment->parse('core/page/section.php');

$formElements = [
    ['field' => '<a class="btn btn-abort" href="' . \rex_url::currentBackendPage() . '">' . \rex_i18n::msg('form_abort') . '</a>'],
    ['field' => '<button class="btn btn-apply rex-form-aligned" type="submit" name="func" value="save"' . \rex::getAccesskey(\rex_i18n::msg('update'), 'apply') . '>' . \rex_i18n::msg('update') . '</button>'],
];
$fragment = new \rex_fragment();
$fragment->setVar('elements', $formElements, FALSE);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new \rex_fragment();
$fragment->setVar('class', 'edit', FALSE);
$fragment->setVar('buttons', $buttons, FALSE);
$sections .= $fragment->parse('core/page/section.php');

echo '<form action="' . \rex_url::currentBackendPage() . '" method="post">' . $sections . '</form>';