<?php
namespace classes;
class Cryptography
{
    public function StrToHex($string)
    {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++)
        {
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }
    public function HexToStr($hex)
    {
        $string = '';
        for ($i=0; $i < strlen($hex)-1; $i+=2)
        {
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }
}
?>
