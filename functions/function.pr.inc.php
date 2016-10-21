<?php

if (!function_exists('pr'))
{
    function pr($data, $color = '#333')
    {
        $a  = debug_backtrace();
        $bt = "line {$a[0]['line']}: {$a[0]['file']}";
        $ic = php_sapi_name() == "cli";
        if ($ic)
        {
            echo "---> {$bt}\n";
        }
        else
        {
            echo '<pre style="clear:both;font-size:9px;font-family:verdana;line-height:12px;background-color:' . $color . ';color:#fff;padding:5px;position:relative;z-index:999999;margin:10px;text-align:left;white-space:pre;">' . $bt . '<br/>';
        }
        print_r($data);
        if (!$ic)
        {
            echo '</pre>';
        }
    }
}
