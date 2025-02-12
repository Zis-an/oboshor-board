<?php

if(!function_exists('is_unicode')) {
    function is_unicode($date)
    {
        if (strlen($date) == strlen(utf8_decode($date))) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('bnFont')) {
    function bnFont($text)
    {
        if (strlen($text) == strlen(utf8_decode($text))) {
            $text =  '<span>' . $text . '</span>';
        } else {
            $text =  '<span class="bn-font">' . $text . '</span>';
        }

        return "<?php echo $text ?>";

    }
}
