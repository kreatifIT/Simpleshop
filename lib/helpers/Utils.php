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

class Utils
{
    protected static $origLocale = '';

    public static function getSetting($key = false, $default = null)
    {
        $settings = (array) \rex::getConfig('simpleshop.Settings');

        if ($key) {
            $settings = array_key_exists($key, $settings) && $settings[$key] !== null ? $settings[$key] : $default;
        }
        return $settings;
    }

    public static function setCalcLocale()
    {
        self::$origLocale = setlocale(LC_NUMERIC, 0);
        $locales          = \ResourceBundle::getLocales('');

        foreach (['en_US', 'C'] as $_locale) {
            if (in_array($_locale, $locales)) {
                break;
            }
        }
        setlocale(LC_NUMERIC, $_locale);
    }

    public static function resetLocale()
    {
        setlocale(LC_NUMERIC, self::$origLocale);
    }

    public static function log($code, $msg, $type, $send_mail = false)
    {
        $email    = \rex_addon::get('simpleshop')
            ->getProperty('debug_email');
        $log_path = \rex_path::addonData('simpleshop', 'log/');
        $log_file = $log_path . date('d') . '.log';
        $msg      = "{$code}: {$msg}\n";
        $type     = strtoupper($type);

        if (!file_exists($log_path)) {
            \rex_dir::create($log_path, true);
        }
        // save to log file
        $append = (int)date('d') == (int)date('d', @filemtime($log_file)) ? FILE_APPEND : null;
        file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] {$type} - " . $msg, $append);

        if ($email && $send_mail) {
            $Mail = new \rex_mailer();
            $Mail->addAddress($email);
            $Mail->isHTML(true);
            $Mail->Subject = "Simpleshop Notice [{$type}]";
            $Mail->Body    = '<div style="font-family:courier;font-size:12px;line-height:14px;width:760px;">' . str_replace("    ", "&nbsp;&nbsp;", nl2br($msg)) . '</div>';
            $Mail->send();
        }
    }

    public static function getConfig($key = null) {
        $settings = \rex::getConfig('simpleshop.Settings');

        if ($key != null) {
            $keys = explode('.', $key);

            foreach ($keys as $item) {
                $settings = $settings[$item];
            }
        }

        return $settings;
    }
}