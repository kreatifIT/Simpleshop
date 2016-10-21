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
    public static function log($code, $msg, $type, $send_mail = FALSE)
    {
        $email    = \rex_addon::get('simpleshop')->getProperty('debug_email');
        $log_path = \rex_path::addonData('simpleshop', 'log/');
        $log_file = $log_path . date('d') . '.log';
        $msg      = "{$code}: {$msg}\n";

        if (!file_exists($log_path))
        {
            \rex_dir::create($log_path, TRUE);
        }
        // save to log file
        $append = (int) date('d') == (int) date('d', @filemtime($log_file)) ? FILE_APPEND : NULL;
        file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] {$type} - ". $msg, $append);

        if ($email && $send_mail)
        {
            $Mail = new \rex_mailer();
            $Mail->addAddress($email);
            $Mail->isHTML(TRUE);
            $Mail->Subject = "Simpleshop Notice [{$type}]";
            $Mail->Body    = '<div style="font-family:courier;font-size:12px;line-height:14px;width:760px;">' . str_replace("    ", "&nbsp;&nbsp;", nl2br($msg)) . '</div>';
            $Mail->send();
        }
    }

    public static function getImageTag($file, $type, $params = [], $callback = NULL)
    {
        $imageTag = '<img src="' . \rex_url::media($file) . '" />';
        return \rex_extension::registerPoint(new \rex_extension_point('simpleshop.getImageTag', $imageTag, [
            'file'     => $file,
            'type'     => $type,
            'params'   => $params,
            'callback' => $callback,
        ]));
    }

    public static function getPagination($totalElements, $elementsPerPage, $gets = [], $_params = [])
    {
        $params = array_merge([
            'get_name'             => 'page',
            'pager_elements_count' => 8,
            'use_request_uri'      => FALSE,
            'show_first_link'      => TRUE,
            'show_last_link'       => TRUE,
            'show_prev_link'       => TRUE,
            'show_next_link'       => TRUE,
        ], $_params);

        $pagination  = [];
        $currentPage = rex_get($params['get_name'], "int", 0);

        if ($params['use_request_uri'])
        {
            $ss         = explode('?', $_SERVER['REQUEST_URI']);
            $paging_url = $ss[0];
        }
        else
        {
            $paging_url = rex_getUrl(\rex_article::getCurrentId());
        }
        if ($totalElements > $elementsPerPage)
        {
            if ($params['show_first_link'] && $currentPage > 0)
            {
                $pagination[] = [
                    "type"  => "first",
                    "class" => "pagination-first",
                    "text"  => "",
                    "url"   => $paging_url . self::getParamString(array_merge($gets, [$params['get_name'] => 0])),
                ];
            }
            if ($currentPage > 0 && $params['show_prev_link'])
            {
                $pagination[] = [
                    "type"  => "prev",
                    "class" => "pagination-previous",
                    "text"  => "",
                    "url"   => $paging_url . self::getParamString(array_merge($gets, [$params['get_name'] => $currentPage - 1])),
                ];
            }
            for ($i = 1; $i <= (int) ceil($totalElements / $elementsPerPage); $i++)
            {
                //dots prefix
                if ($i < ($currentPage - floor($params['pager_elements_count'] / 2)))
                {
                    $i            = $currentPage - floor($params['pager_elements_count'] / 2);
                    $pagination[] = [
                        "type"  => "ellipsis",
                        "class" => "pagination-ellipsis",
                        "text"  => "",
                        "url"   => $paging_url . self::getParamString(array_merge($gets, [$params['get_name'] => $i - 1])),
                    ];
                }
                //dots suffix
                else if ($i > $currentPage + ceil($params['pager_elements_count'] / 2))
                {
                    $pagination[] = [
                        "type"  => "ellipsis",
                        "class" => "pagination-ellipsis",
                        "text"  => "",
                        "url"   => $paging_url . self::getParamString(array_merge($gets, [$params['get_name'] => $i - 1])),
                    ];
                    break; //stops iteration
                }
                else
                {
                    if ($currentPage != $i - 1)
                    {
                        $pagination[] = [
                            "type"  => "page",
                            "class" => "",
                            "text"  => $i,
                            "url"   => $paging_url . self::getParamString(array_merge($gets, [$params['get_name'] => $i - 1])),
                        ];
                    }
                    else
                    {
                        $pagination[] = [
                            "type"  => "active",
                            "class" => "pagination-active current",
                            "text"  => $i,
                            "url"   => $paging_url . self::getParamString(array_merge($gets, [$params['get_name'] => $i - 1])),
                        ];
                    }
                }
            }
            if (($currentPage + 1) <= ((int) ceil($totalElements / $elementsPerPage) - 1) && $params['show_next_link'])
            {
                $pagination[] = [
                    "type"  => "next",
                    "class" => "pagination-next",
                    "text"  => "",
                    "url"   => $paging_url . self::getParamString(array_merge($gets, [$params['get_name'] => $currentPage + 1])),
                ];
            }
            if ($currentPage < ((int) ceil($totalElements / $elementsPerPage) - 1) && $params['show_last_link'])
            {
                $pagination[] = [
                    "type"  => "last",
                    "class" => "pagination-last",
                    "text"  => "",
                    "url"   => $paging_url . self::getParamString(array_merge($gets, [$params['get_name'] => ((int) ceil($totalElements / $elementsPerPage) - 1)])),
                ];
            }
        }
        return $pagination;
    }

    private static function getParamString($params, $divider = '&amp;')
    {
        $_p = [];
        if (is_array($params))
        {
            foreach ($params as $key => $value)
            {
                $_p[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        elseif ($params != '')
        {
            $_p[] = $params;
        }
        $string = implode($divider, $_p);
        return strlen($string) ? '?' . $string : '';
    }
}