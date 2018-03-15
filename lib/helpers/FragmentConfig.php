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


use Kreatif\Project\Settings;

class FragmentConfig
{
    public static $data = [
        'auth'     => [
            'has_registration'      => true,
            'has_password_recovery' => true,
            'css_class'             => [
                'wrapper' => 'margin-large-top margin-large-bottom',
                'buttons' => 'expanded margin-bottom',
            ],
        ],
        'customer' => [
            'css_class' => [
                'form_fields' => 'medium-6',
            ],
        ],
        'cart'     => [
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
                'has_go_ahead'    => true,
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
        'checkout' => [
            'has_coupons' => true,
            'steps'       => [
                'invoice_address',
                'shipping_address',
                'shipping||payment',
                'show-summary',
            ],
            'email'       => [
                'is_order_complete'         => true,
                'use_invoicing'             => true,
                'has_image'                 => false,
                'has_remove_button'         => false,
                'has_quantity_control'      => false,
                'has_global_refresh_button' => false,
                'has_edit_link'             => false,
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

        'email_styles' => [
            'body'          => 'border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%;',
            'tr'            => 'padding:0;text-align:left;vertical-align:top;',
            'th'            => 'Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left;',
            'td'            => '-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border:1px solid #cacaca;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;hyphens:auto;line-height:1.6;margin:0;padding:10px;text-align:left;vertical-align:top;word-wrap:break-word;',
            'h3'            => 'Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left;word-wrap:normal;',
            'p'             => 'Margin:0;Margin-bottom:10px;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left;',
            'code'          => 'font-family:Consolas,"Liberation Mono",Courier,monospace;background:#f9f9f9;border:1px solid #cacaca;padding:5px 8px;margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;text-align:left;',
            'callout'       => 'Margin-bottom:16px;border-collapse:collapse;border-spacing:0;margin-bottom:16px;padding:0;text-align:left;vertical-align:top;width:100%;',
            'callout_inner' => 'Margin:0;background:#f3f3f3;border:1px solid #cacaca;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:10px;text-align:left;width:100%;',
        ],


        'yform_fields' => [
            'rex_shop_customer_address' => [
                '_excludedFields' => ['status', 'customer_id'],
                'country'         => [
                    'just_names' => true,
                ],
            ],
            'rex_shop_customer'         => [
                '_excludedFields' => ['lang_id', 'addresses', 'status', 'lastlogin', 'updatedate', 'created'],
                'ctype'           => [
                    'css_class' => 'column" onchange="Simpleshop.changeCType(this, \'\')"',
                ],
            ],
        ],
    ];

    public static function getValue($selector, $default = null)
    {
        $result = self::$data;
        $keys   = explode('.', $selector);

        foreach ($keys as $key) {
            if (array_key_exists($key, $result)) {
                $result = $result[$key];
            }
            else {
                $result = $default;
                break;
            }
        }
        return $result;
    }
}