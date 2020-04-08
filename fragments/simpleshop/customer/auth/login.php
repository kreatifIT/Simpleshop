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

use Kreatif\Form;


$referer  = $this->getVar('referer');
$Config   = FragmentConfig::getValue('auth');
$errors   = [];
$id       = 'auth-' . \rex_article::getCurrentId();
$sid      = "form-{$id}";
$action   = rex_request('action', 'string');
$Settings = \rex::getConfig('simpleshop.Settings');

if (rex_post('action', 'string') == 'login') {
    if (Customer::login(rex_post('uname', 'string'), rex_post('pwd', 'string'))) {
        $referer = rex_session('login_referer', 'string');
        rex_unset_session('login_referer');

        unset($_GET['action']);

        switch ($referer) {
            case 'account':
                rex_redirect($Settings['linklist']['dashboard'], null, ['ts' => time()]);
                break;
            default:
                if ($referer == '') {
                    rex_redirect(\rex_article::getCurrentId(), null, array_merge($_GET, ['ts' => time()]));
                } else if (strlen($referer)) {
                    header('Location: ' . $referer);
                    exit;
                }
                break;
        }
    } else {
        $errors[] = '###error.login_failed###';
    }
}
if (rex_get('referer', 'string') == 'return') {
    rex_set_session('login_referer', rex_server('HTTP_REFERER'));
} else if (rex_get('referer', 'string') == 'account') {
    rex_set_session('login_referer', 'account');
} else if (strlen($referer)) {
    rex_set_session('login_referer', $referer);
}

unset($_GET['action']);

$texts = [
    'registration_info_text' => \Wildcard::get('label.registration_info_text'),
    'login_info_text'        => \Wildcard::get('label.login_info_text'),
    'pwd_recovery_info_text' => \Wildcard::get('label.pwd_recovery_info_text'),
];

?>
<div id="<?= $sid ?>" class="section-wrapper auth-wrapper <?= $Config['css_class']['wrapper'] ?>" data-auth-wrapper data-scroll-offset="100">
    <div class="grid-x grid-margin-x <?= $Config['has_registration'] ? '' : 'align-center' ?>">

        <div class="login-form large-4 medium-6 cell <?= $action == 'recover' ? 'hide' : '' ?>">
            <form action="<?= rex_getUrl(null, null, array_merge($_GET, ['ts' => time()])) ?>#-<?= $sid ?>" method="post">
                <div class="login-panel background-gray">
                    <h2 class="margin-small-bottom heading"><?= ucfirst(\Sprog\Wildcard::get('action.login')) ?></h2>

                    <?php if (strlen($texts['login_info_text'])): ?>
                        <p><?= $texts['login_info_text'] ?></p>
                    <?php endif; ?>

                    <?php if (count($errors)): ?>
                        <div class="callout alert">
                            <?= implode('<br/>', $errors) ?>
                        </div>
                    <?php endif; ?>

                    <div class="input-group">
                        <input type="email" class="input-group-field" name="uname"
                               value="<?= rex_post('uname', 'string') ?>" placeholder="###label.email###"
                               tabindex="103"/>
                    </div>
                    <div class="input-group">
                        <input type="password" class="input-group-field" name="pwd" value=""
                               placeholder="###label.password###" tabindex="104"/>
                    </div>
                    <button type="submit" class="button <?= $Config['css_class']['buttons'] ?>" tabindex="105" name="action"
                            value="login"><?= ucfirst(\Sprog\Wildcard::get('action.login')) ?></button>

                    <?php if ($Config['has_password_recovery']): ?>
                        <div class="login-form-passwort-reset">
                            <a class="text-link" href="javascript:;"
                               onclick="Simpleshop.toggleAuth(this, '.recovery-form', '.login-form')">###label.password_forgotten###.</a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if ($Config['has_password_recovery']): ?>
            <?php
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // PASSWORD RECOVERY
            ?>
            <div class="recovery-form large-4 medium-6 cell <?= $action == 'recover' ? '' : 'hide' ?>">
                <div class="login-panel background-gray">
                    <h2 class="margin-small-bottom heading"><?= ucfirst(\Sprog\Wildcard::get('label.password_forgotten')) ?></h2>

                    <?php if (strlen($texts['pwd_recovery_info_text'])): ?>
                        <p><?= $texts['pwd_recovery_info_text'] ?></p>
                    <?php endif; ?>

                    <?php
                    $recoverySuccess = false;

                    if (rex_post('action', 'string') == 'recover') {
                        $recoverySuccess = true;
                        $email           = rex_post('uname', 'string');
                        Customer::resetPassword($email);
                    }

                    ?>
                    <?php if ($recoverySuccess): ?>
                        <div class="callout success">###notif.password_reset_msg###</div>
                    <?php else: ?>
                        <form action="#-<?= $sid ?>" method="post">
                            <input type="email" name="uname" placeholder="###label.email###"
                                   value="<?= rex_post('uname', 'string'); ?>">
                            <button type="submit" class="button <?= $Config['css_class']['buttons'] ?>"
                                    name="action" value="recover">
                                ###action.reset###
                            </button>
                        </form>
                    <?php endif ?>
                    <div class="margin-small-top">
                        <a href="javascript:;" onclick="Simpleshop.toggleAuth(this, '.login-form', '.recovery-form')"
                           class="text-link">###action.back_to_login###</a>.
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($Config['has_registration']): ?>
            <?php
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // REGISTRATION
            ?>
            <div class="register-form large-8 medium-7 cell">
                <div class="login-panel background-gray">
                    <h2 class="margin-small-bottom heading"><?= ucfirst(\Sprog\Wildcard::get('action.register')) ?></h2>
                    <?php if (strlen($texts['registration_info_text'])): ?>
                        <p><?= $texts['registration_info_text'] ?></p>
                    <?php endif; ?>

                    <?php
                    $this->subfragment('simpleshop/customer/auth/registration.php');
                    ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
