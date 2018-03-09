<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 08.03.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;


class FragmentConfig
{
    public static $data = [
        'auth' => [
            'has_registration'      => true,
            'has_password_recovery' => true,
            'css_class'             => [
                'wrapper' => 'margin-large-top margin-large-bottom',
                'buttons' => 'expanded margin-bottom',
            ],
        ],
        'cart' => [
            'has_remove_button' => true,
            'has_image'         => true,
            'button'            => [
                'has_quantity_control'   => true,
                'has_quantity'           => true,
                'quantity_is_writeable'  => true,
                'is_disabled'            => false,
                'has_add_to_cart_button' => false,
                'has_request_button'     => false,
                'has_detail_button'      => false,
            ],
            'table-wrapper'     => [
                'class'           => 'stack',
                'btn_ahead_class' => 'secondary',
                'ahead_url'       => '',
                'back_url'        => '',
            ],
            'item'              => [
                'class'                => '',
                'email_tpl_styles'     => [],
                'has_image'            => true,
                'has_remove_button'    => true,
                'has_refresh_button'   => true,
                'has_quantity_control' => true,
            ],
            'summary'           => [
                'css_class' => [
                    'table' => '',
                ],
            ],
        ],

        'styles' => [
            'total' => '',
            'table' => '',
            'tr'    => '',
            'th'    => '',
            'td'    => '',
            'h1'    => '',
            'h2'    => '',
            'h3'    => '',
            'h4'    => '',
            'h5'    => '',
            'h6'    => '',
            'p'     => '',
            'code'  => '',
        ],
    ];

    public static function getValue($selector)
    {
        $result = self::$data;
        $keys   = explode('.', $selector);

        foreach ($keys as $key) {
            if (array_key_exists($key, $result)) {
                $result = $result[$key];
            }
            else {
                $result = null;
                break;
            }
        }
        return $result;
    }
}