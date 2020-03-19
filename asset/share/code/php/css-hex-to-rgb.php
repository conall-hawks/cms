<?php

function css_hex_to_rgb($hex = '#000000'){

    /* Get hex value and validate. */
    $hex = str_replace('#', '', $hex);
    if(!ctype_xdigit($hex)) throw new Exception('Not a hexadecimal number.', 1);

    /* Defaults. */
    $red    = 0;
    $green  = 0;
    $blue   = 0;
    $alpha  = 1;

    /* Get length to determine shortened or normal hex representation. */
    $length = strlen($hex);

    /* Shortened. */
    if($length >= 1 && $length <= 4){
        if($length < 3) $hex = str_pad($hex, 3, '0');
        $red   = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
        $green = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
        $blue  = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
        if($length === 4) $alpha = hexdec(substr($hex, 3, 1).substr($hex, 3, 1)) / 255;
    }

    /* Normal. */
    elseif($length >= 5){
        if($length < 6) $hex = str_pad($hex, 6, '0');
        $red   = hexdec(substr($hex, 0, 2));
        $green = hexdec(substr($hex, 2, 2));
        $blue  = hexdec(substr($hex, 4, 2));
        if($length > 6){
            if($length < 8) $hex = str_pad($hex, 8, '0');
            $alpha = hexdec(substr($hex, 6, 2)) / 255;
        }
    }

    /* Done! */
    if($alpha !== 1){
        return 'rgba('.$red.', '.$green.', '.$blue.', '.$alpha.')';
    }else{
        return 'rgb(' .$red.', '.$green.', '.$blue.')';
    }
}
