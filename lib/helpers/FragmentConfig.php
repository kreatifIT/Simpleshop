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
        'has_variants'                 => false,
        'xml_general_line_description' => 'Product line description',
        'auth'                         => [
            'has_registration'         => true,
            'has_password_recovery'    => true,
            'registration_excl_fields' => [],
            'css_class'                => [
                'wrapper' => 'margin-large-top margin-large-bottom',
                'buttons' => 'expanded margin-bottom',
            ],
        ],
        'customer'                     => [
            'css_class' => [
                'form_fields' => 'medium-6',
            ],
        ],
        'cart'                         => [
            'has_remove_button' => true,
            'has_image'         => true,
            'has_coupons'       => true,
            'button'            => [
                'has_quantity_control'   => true,
                'has_quantity'           => true,
                'is_disabled'            => false,
                'has_add_to_cart_button' => false,
                'has_request_button'     => false,
                'has_detail_button'      => false,
                'css_class'              => [
                    'button' => 'button',
                ],
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
        'checkout'                     => [
            'has_coupons'          => true,
            'has_summary_footer'   => true,
            'show_tax_info'        => true,
            'steps'                => [
                'invoice_address',
                'shipping_address',
                'shipping||payment',
                'show-summary',
            ],
            'email'                => [
                'is_order_complete'         => true,
                'has_image'                 => false,
                'has_remove_button'         => false,
                'has_quantity_control'      => false,
                'has_global_refresh_button' => false,
                'has_edit_link'             => false,
            ],
            'generate_pdf'         => null,
            'invoice_excl_fields'  => [],
            'shipping_excl_fields' => ['ctype', 'vat_num', 'fiscal_code'],
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
            'use_mail_styles' => false,
            'table'           => 'style="border:none;border-collapse:collapse;width:100%;"',
            'body'            => 'style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%;"',
            'tr'              => 'style="padding:0;text-align:left;vertical-align:top;"',
            'th'              => 'style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left;"',
            'td'              => 'style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;hyphens:auto;line-height:1.6;margin:0;padding:10px;text-align:left;vertical-align:top;word-wrap:break-word;"',
            'prod-th'         => 'style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;line-height:1.6;margin:0;padding:0;text-align:left;"',
            'prod-tr'         => 'style="padding:0;text-align:left;vertical-align:top;"',
            'prod-td'         => 'style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-top:1px solid #cacaca;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;hyphens:auto;line-height:1.6;margin:0;padding:10px 0;text-align:left;vertical-align:top;word-wrap:break-word;"',
            'sum-td'          => 'style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;hyphens:auto;line-height:1.6;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word;"',
            'sum-td-right'    => 'style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;hyphens:auto;line-height:2;margin:0;padding:0;text-align:right;vertical-align:top;word-wrap:break-word;"',
            'h2'              => 'style="Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:600;line-height:1.6;margin:0;padding:0;text-align:left;word-wrap:normal;"',
            'h3'              => 'style="Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left;word-wrap:normal;"',
            'p'               => 'style="Margin:0;Margin-bottom:10px;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left;"',
            'code'            => 'style="font-family:Consolas,"Liberation Mono",Courier,monospace;background:#f9f9f9;border:1px solid #cacaca;padding:5px 8px;margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;text-align:left;"',
            'callout'         => 'style="Margin-bottom:16px;border-collapse:collapse;border-spacing:0;margin-bottom:16px;padding:0;text-align:left;vertical-align:top;width:100%;"',
            'callout_inner'   => 'style="Margin:0;background:#f3f3f3;border:1px solid #cacaca;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:10px;text-align:left;width:100%;"',
            'coupon_wrapper'  => 'style="Margin:0 0 20px 0;background:#333333;border:1px solid #cacaca;color:#ffffff;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;padding:10px;text-align:left;"',
            'coupon_heading'  => 'style="Margin:0;color:#ffffff;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;padding:0;text-align:left;"',
            'coupon_code'     => 'style="Margin:10px 0 0 0;color:#ffffff;font-family:Helvetica,Arial,sans-serif;font-size:24px;font-weight:400;line-height:1.6;padding:0;text-align:left;"',
        ],


        // FIELD DEFINITIONS
        'yform_fields' => [
            'rex_shop_customer_address' => [
                '_fieldDefaults'  => ['css_class' => 'cell medium-6'],
                '_excludedFields' => ['status', 'customer_id'],
                'country'         => [
                    'just_names' => true,
                ],
            ],
            'rex_shop_customer'         => [
                '_fieldDefaults'  => ['css_class' => 'cell medium-6'],
                '_excludedFields' => ['invoice_address_id', 'lang_id', 'addresses', 'status', 'lastlogin', 'updatedate', 'created'],
            ],
        ],
    ];

    public static function getValue($selector, $default = null)
    {
        $result = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.FragmentConfig.getValue', self::$data, [
            'selector' => $selector,
            'default'  => $default,
        ]));
        $keys   = explode('.', $selector);

        foreach ($keys as $key) {
            if (array_key_exists($key, $result)) {
                if ($key == 'styles' && self::$data['email_styles']['use_mail_styles']) {
                    $key = 'email_styles';
                }
                $result = $result[$key];
            } else {
                $result = $default;
                break;
            }
        }
        return $result;
    }
}