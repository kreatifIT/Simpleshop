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

?>
<!-- spacer -->
<table class="row"
       style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
    <tbody>
    <tr style="padding:0;text-align:left;vertical-align:top">
        <th class="small-12 large-12 columns first last"
            style="Margin:0 auto;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0 auto;padding:0;padding-bottom:16px;padding-left:16px;padding-right:16px;text-align:left;width:564px">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left">
                        <table class="spacer"
                               style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                            <tbody>
                            <tr style="padding:0;text-align:left;vertical-align:top">
                                <td height="20px"
                                    style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:400;hyphens:auto;line-height:20px;margin:0;mso-line-height-rule:exactly;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
                                    &#xA0;</td>
                            </tr>
                            </tbody>
                        </table>
                    </th>
                    <th class="expander"
                        style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>
<!-- logo -->
<table class="row"
       style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
    <tbody>
    <tr style="padding:0;text-align:left;vertical-align:top">
        <th class="small-6 large-4 columns first"
            style="Margin:0 auto;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0 auto;padding:0;padding-bottom:16px;padding-left:16px;padding-right:8px;text-align:left;width:50%">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left">
                        <?php
                        $logo_path = $this->getVar('logo_path', '');
                        if (strlen($logo_path)):
                            echo \Kreatif\Resource::getImageTag($logo_path, '', [
                                'alt' => "###company.name###",
                                'style' => "-ms-interpolation-mode:bicubic;clear:both;display:block;max-width:100%;outline:0;text-decoration:none;width:auto",
                            ]);
                            ?>
                        <?php else: ?>
                            ###company.name###
                        <?php endif; ?>
                    </th>
                </tr>
            </table>
        </th>
        <th class="small-6 large-8 columns last"
            style="Margin:0 auto;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0 auto;padding:0;padding-bottom:16px;padding-left:8px;padding-right:16px;text-align:left;width:50%">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left">
                        <p class="text-right header-address"
                           style="Margin:0;Margin-bottom:10px;color:#8a8a8a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:700;line-height:1.6;margin:0;margin-bottom:10px;padding:0;text-align:right">
                            ###company.street###<br>
                            ###company.postal### ###company.location###<br>
                            ###company.region### ###company.province###<br>
                        </p>
                    </th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>
<!-- rule -->
<table class="row"
       style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
    <tbody>
    <tr style="padding:0;text-align:left;vertical-align:top">
        <th class="small-12 large-12 columns first last"
            style="Margin:0 auto;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0 auto;padding:0;padding-bottom:16px;padding-left:16px;padding-right:16px;text-align:left;width:564px">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left">
                        <hr>
                    </th>
                    <th class="expander"
                        style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>


