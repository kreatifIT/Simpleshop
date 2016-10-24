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

$fragment_path = $this->getVar('fragment_path');
$base_url = $this->getVar('base_url');

$text_align = 'left';
$bg_color = '#f2f2f2';


if (!$fragment_path)
{
    throw new \phpmailerException('No fragment_path set!');
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

    <title>###company.name###</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <!--[if !mso]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <!--<![endif]-->

    <style type="text/css">

        .ReadMsgBody {
            width: 100%;
            background-color: #ffffff;
        }

        .ExternalClass {
            width: 100%;
            background-color: #ffffff;
        }

        body {
            width: 100%;
            background-color: <?= $bg_color ?>;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            font-family: Arial, Times, serif
        }

        table {
            border-collapse: collapse !important;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        @-ms-viewport {
            width: device-width;
        }

        @media only screen and (max-width: 639px) {
            .wrapper {
                width: 100%;
                padding: 0 !important;
            }
        }

        @media only screen and (max-width: 480px) {
            .centerClass {
                margin: 0 auto !important;
            }

            .imgClass {
                width: 100% !important;
                height: auto;
            }

            .wrapper {
                width: 320px;
                padding: 0 !important;
            }

            .header {
                width: 320px;
                padding: 0 !important;
                background-image: url(http://placehold.it/320x400) !important;
            }

            .container {
                width: 300px;
                padding: 0 !important;
            }

            .mobile {
                width: 300px;
                display: block;
                padding: 0 !important;
                text-align: center !important;
            }

            .mobile50 {
                width: 300px;
                padding: 0 !important;
                text-align: center;
            }

            *[class="mobileOff"] {
                width: 0px !important;
                display: none !important;
            }

            *[class*="mobileOn"] {
                display: block !important;
                max-height: none !important;
            }
        }


    </style>

    <!--[if gte mso 15]>
    <style type="text/css">
        table {
            font-size: 1px;
            line-height: 0;
            mso-margin-top-alt: 1px;
            mso-line-height-rule: exactly;
        }

        * {
            mso-line-height-rule: exactly;
        }
    </style>
    <![endif]-->

</head>
<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0"
      style="background-color:<?= $bg_color ?>;  font-family:Arial,serif; margin:0; padding:0; min-width: 100%; -webkit-text-size-adjust:none; -ms-text-size-adjust:none;">

<!--[if !mso]><!-- -->
<img style="min-width:800px; display:block; margin:0; padding:0" class="mobileOff" width="800" height="1"
     src="<?= $base_url . ltrim(\rex_url::addonAssets('simpleshop', 'img/email/spacer.gif'), '/'); ?>">
<!--<![endif]-->

<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" st-sortable="left-image">
    <tbody>
    <tr>
        <td width="100%" valign="top" align="center">

            <!-- Start Wrapper -->
            <table width="800" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                <tbody>
                <tr>
                    <td align="left">

                        <!-- Start Container -->
                        <table width="800" cellpadding="0" cellspacing="0" border="0" class="container">
                            <tbody>
                            <tr>
                                <td height="20" style="font-size:10px; line-height:10px;"></td><!-- Spacer -->
                            </tr>
                            <tr>
                                <td width="800" class="mobile" valign="top"
                                    style="font-family:arial; font-size:12px; line-height:18px; padding-left:20px;padding-right:20px;">

                                    <?= $this->subfragment("simpleshop/email/{$fragment_path}.php"); ?>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- End Container -->

                    </td>
                </tr>
                </tbody>
            </table>
            <!-- End Wrapper -->

        </td>
    </tr>
    </tbody>
</table>


<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" st-sortable="left-image">
    <tbody>
    <tr>
        <td width="100%" valign="top" align="center">

            <?= $this->subfragment('simpleshop/email/footer.php') ?>

        </td>
    </tr>
    </tbody>
</table>

</body>
</html>