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


$loginDone = false;
$referer   = $this->getVar('referer');
$Config    = FragmentConfig::getValue('auth');
$errors    = [];
$id        = 'auth-' . \rex_article::getCurrentId();
$sid       = "form-{$id}";
$action    = rex_request('action', 'string');
unset($_GET['action']);

$texts = [
    'registration_info_text' => \Wildcard::get('label.registration_info_text'),
    'login_info_text'        => \Wildcard::get('label.login_info_text'),
    'pwd_recovery_info_text' => \Wildcard::get('label.pwd_recovery_info_text'),
];

if ($action == 'activate_customer') {
    $isActivated = Customer::activate(rex_get('hash', 'string'));
}
if (rex_get('referer', 'string') == 'return') {
    rex_set_session('login_referer', rex_server('HTTP_REFERER'));
} else if (rex_get('referer', 'string') == 'account') {
    rex_set_session('login_referer', 'account');
} else if (strlen($referer)) {
    rex_set_session('login_referer', $referer);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


?>
<div id="<?= $sid ?>" class="section-wrapper auth-wrapper <?= $Config['css_class']['wrapper'] ?>" data-auth-wrapper>
    <div class="grid-container">
        <?php if (isset($isActivated)): ?>
            <div class="callout <?= $isActivated == 1 ? 'success' : 'alert' ?>">
                <?php
                switch ($isActivated) {
                    case 2:
                        $msg = '###label.reg_activation_already_done###';
                        break;

                    case 1:
                        $msg = '###label.reg_activation_successful###';
                        break;

                    case 0:
                    default:
                        $msg = '###label.reg_activation_failed###';
                        break;
                }
                echo $msg;
                ?>
            </div>
        <?php endif; ?>

        <div class="grid-x grid-margin-x <?= $Config['has_registration'] ? '' : 'align-center' ?>">

            <div class="login-form large-4 medium-6 cell" id="login-wrapper-<?= $sid ?>">
                <?php if ($Config['has_password_recovery'] && $action == 'recover'): ?>
                    <?php
                    // PASSWORD RECOVERY
                    ?>
                    <div class="login-panel">
                        <h2 class="heading small"><?= ucfirst(\Wildcard::get('label.password_forgotten')) ?></h2>

                        <?php if (strlen($texts['pwd_recovery_info_text'])): ?>
                            <div class="text">
                                <?= $texts['pwd_recovery_info_text'] ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        $fragment = new \rex_fragment();
                        $fragment->setVar('sid', $sid);
                        echo $fragment->parse('simpleshop/customer/auth/password_recovery.php');
                        ?>

                        <div class="margin-small-top">
                            <?php
                            $pjaxData = rex_escape(json_encode([
                                'container' => '#login-wrapper-' . $sid,
                                'fragment'  => '#login-wrapper-' . $sid,
                                'push'      => true,
                            ]), 'html_attr');
                            ?>
                            <a href="<?= rex_getUrl(null, null, array_merge($_GET, ['action' => 'login'])); ?>" class="text-link" data-pjax="<?= $pjaxData ?>">
                                ###action.back_to_login###
                            </a>.
                        </div>
                    </div>
                <?php else: ?>
                    <?php
                    // LOGIN
                    ?>
                    <div class="login-panel">
                        <h2 class="heading small">
                            <?= ucfirst(\Wildcard::get('action.login')) ?>
                        </h2>

                        <?php if (strlen($texts['login_info_text'])): ?>
                            <div class="text"><?= $texts['login_info_text'] ?></div>
                        <?php endif; ?>

                        <?php
                        $fragment = new \rex_fragment();
                        $fragment->setVar('sid', $sid);
                        echo $fragment->parse('simpleshop/customer/auth/login.php');
                        ?>

                        <?php if ($Config['has_password_recovery']): ?>
                            <div class="margin-small-top">
                                <?php
                                $linkUrl  = rex_getUrl(null, null, array_merge($_GET, ['action' => 'recover']));
                                $pjaxData = rex_escape(json_encode([
                                    'container' => '#login-wrapper-' . $sid,
                                    'fragment'  => '#login-wrapper-' . $sid,
                                    'push'      => true,
                                ]), 'html_attr');
                                ?>
                                <a class="text-link" href="<?= $linkUrl ?>" data-pjax="<?= $pjaxData ?>">
                                    ###label.password_forgotten###
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>


            <?php if ($Config['has_registration']): ?>
                <?php
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                // REGISTRATION
                ?>
                <div class="register-form large-8 cell">
                    <div class="login-panel">
                        <h2 class="heading small">
                            <?= ucfirst(\Wildcard::get('action.register')) ?>
                        </h2>

                        <?php if (strlen($texts['registration_info_text'])): ?>
                            <div class="text">
                                <?= $texts['registration_info_text'] ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        $fragment = new \rex_fragment();
                        $fragment->setVar('sid', $sid);
                        echo $fragment->parse('simpleshop/customer/auth/registration.php');
                        ?>

                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
