<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 08.08.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$sql         = rex_sql::factory();
$image_types = [
    'product_gallery_thumb' => [
        'description' => '50x50',
        'effects'     => [
            [
                'name'   => 'focuspoint_fit',
                'params' => json_encode([
                    'rex_effect_focuspoint_fit' => [
                        'rex_effect_focuspoint_fit_width'  => 50,
                        'rex_effect_focuspoint_fit_height' => 50,
                        'rex_effect_focuspoint_fit_hpos'   => 50,
                        'rex_effect_focuspoint_fit_vpos'   => 50,
                        'rex_effect_focuspoint_fit_fp'     => "Fokuspunkt des Bildes, Fallback allgemeiner Bezugspunkt",
                        'rex_effect_focuspoint_fit_zoom'   => "Ausschnitt gr\u00f6\u00dftm\u00f6glich w\u00e4hlen (100%)",
                    ],
                ]),
            ],
        ],
    ],
    'email_product_thumb'   => [
        'description' => '70x70',
        'effects'     => [
            [
                'name'   => 'resize',
                'params' => json_encode([
                    'rex_effect_resize' => [
                        'rex_effect_resize_width'         => 70,
                        'rex_effect_resize_height'        => 70,
                        'rex_effect_resize_style'         => 'maximum',
                        'rex_effect_resize_allow_enlarge' => 'not_enlarge',
                    ],
                ]),
            ],
            [
                'name'   => 'workspace',
                'params' => json_encode([
                    'rex_effect_workspace' => [
                        'rex_effect_workspace_width'           => 70,
                        'rex_effect_workspace_height'          => 70,
                        'rex_effect_workspace_hpos'            => 'center',
                        'rex_effect_workspace_vpos'            => 'middle',
                        'rex_effect_workspace_set_transparent' => 'colored',
                        'rex_effect_workspace_bg_r'            => 255,
                        'rex_effect_workspace_bg_g'            => 255,
                        'rex_effect_workspace_bg_b'            => 255,
                    ],
                ]),
            ],
        ],
    ],
    'product_thumb'         => [
        'description' => '360x360',
        'effects'     => [
            [
                'name'   => 'resize',
                'params' => json_encode([
                    'rex_effect_resize' => [
                        'rex_effect_resize_width'         => 360,
                        'rex_effect_resize_height'        => 360,
                        'rex_effect_resize_style'         => 'maximum',
                        'rex_effect_resize_allow_enlarge' => 'not_enlarge',
                    ],
                ]),
            ],
            [
                'name'   => 'workspace',
                'params' => json_encode([
                    'rex_effect_workspace' => [
                        'rex_effect_workspace_width'           => 360,
                        'rex_effect_workspace_height'          => 360,
                        'rex_effect_workspace_hpos'            => 'center',
                        'rex_effect_workspace_vpos'            => 'middle',
                        'rex_effect_workspace_set_transparent' => 'colored',
                        'rex_effect_workspace_bg_r'            => 255,
                        'rex_effect_workspace_bg_g'            => 255,
                        'rex_effect_workspace_bg_b'            => 255,
                    ],
                ]),
            ],
        ],
    ],
];


foreach ($image_types as $key => $data) {
    $sql->setTable('rex_media_manager_type');
    $sql->setValue('name', $key);
    $sql->setValue('description', $data['description']);
    $sql->insert();

    $lid = $sql->getLastId();

    foreach ($data['effects'] as $index => $effect) {
        $sql->setTable('rex_media_manager_type_effect');
        $sql->setValue('type_id', $lid);
        $sql->setValue('effect', $effect['name']);
        $sql->setValue('priority', $index + 1);
        $sql->setValue('updatedate', date('Y-m-d H:i:s'));
        $sql->setValue('createdate', date('Y-m-d H:i:s'));
        $sql->setValue('updateuser', 'simpleshop-addon');
        $sql->setValue('createuser', 'simpleshop-addon');
        $sql->setValue('parameters', $effect['params']);
        $sql->insert();
    }
}