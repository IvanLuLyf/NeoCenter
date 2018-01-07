<?php
/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/7
 * Time: 14:46
 */
function getIdCodes($s)
{
    $base32 = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
        'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
        'y', 'z', '0', '1', '2', '3', '4', '5'
    );
    $hex = md5($s);
    $hexLen = strlen($hex);
    $subHexLen = $hexLen / 8;
    $output = array();
    for ($i = 0; $i < $subHexLen; $i++) {
        $subHex = substr($hex, $i * 8, 8);
        $int = 0x3FFFFFFF & (hexdec('0x' . $subHex));
        $out = '';
        for ($j = 0; $j < 6; $j++) {
            $val = 0x0000001F & $int;
            $out .= $base32[$val];
            $int = $int >> 5;
        }
        $output[] = $out;
    }
    return $output;
}