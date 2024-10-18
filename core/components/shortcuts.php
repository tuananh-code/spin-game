<?php 
# @*************************************************************************@
# @ Software author: Mansur Terla (Mansur_TL)                               @
# @ UI/UX Designer & Web developer ;)                                       @
# @                                                                         @
# @*************************************************************************@
# @ Instagram: https://www.instagram.com/mansur_tl                          @
# @ VK: https://vk.com/mansur_tl_uiux                                       @
# @ Envato: http://codecanyon.net/user/mansur_tl                            @
# @ Behance: https://www.behance.net/mansur_tl                              @
# @ Telegram: https://t.me/mansurtl_contact                                 @
# @*************************************************************************@
# @ E-mail: mansurtl.contact@gmail.com                                      @
# @ Website: https://www.mansurtl.com                                       @
# @*************************************************************************@
# @ ColibriSM - The Ultimate Social Network PHP Script                      @
# @ Copyright (c)  ColibriSM. All rights reserved                           @
# @*************************************************************************@

function fetch_or_get(&$var, $alt = null) {
    if (empty($var) != true) {
        return $var;
    }
    else {
        return $alt;
    }
}

function formattedDateTime(&$var, $alt = null)
{
    if (empty($var) != true) {
        list($datePart, $timePart) = explode('T', $var);

        list($hour, $minute) = explode(':', $timePart);
        if (strlen($minute) === 1) {
            $minute = '0' . $minute;
        }

        $formattedDateTime = $datePart . ' ' . $hour . ':' . $minute . ':00';
        return $formattedDateTime;
    } else {
        return $alt;
    }
}

function cl_strf() { 
	return call_user_func_array("sprintf", func_get_args());
}

function not_num(&$var) {
    return (empty($var) || is_numeric($var) != true || $var < 1) ? true : false;
}

function not_empty(&$var) {
    return (empty($var) != true);
}

function json($array = array(), $seril = null) {
    if ($seril) {
        return json_encode($array, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    else {
        
        $array = (empty($array)) ? "[]" : $array;

        return json_decode($array, true);
    }
}

function len($string = '') {

    if (empty($string)) {
        return 0;
    }
    
    return mb_strlen($string);
}

function len_between($string = '',$s = 0, $e = 0) {
    return ((mb_strlen($string) >= $s) && (mb_strlen($string) <= $e));
}

function is_posnum($var) {
    return (is_numeric($var) == true && $var >= 1) ? true : false;
}

function is_url($url = null) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

function are_all($arr = array(), $type = none) {

    if (empty($arr) || is_array($arr) != true) {
        return false;
    }

    else if(empty($type) || in_array($type, array('numeric', 'string')) != true) {
        return false;
    }

    if ($type == 'numeric') {
        foreach ($arr as $val) {
            if (is_numeric(($val)) != true) {
                return false;
            }
        }
    } 

    else if ($type == 'string') {
        foreach ($arr as $val) {
            if (is_string(($val)) != true) {
                return false;
            }
        }
    }

    return true;
}

function array_max_key($array = array()) {
    if (empty($array) || is_array($array) != true) {
        return false;
    }

    else {
        foreach ($array as $key => $val) {
            if ($val == max($array)) {
                return $key;
            } 
        }
    }
}

function pre($op = null, $exit = false) {
    echo "<pre>";
    print_r($op);
    echo "</pre>";

    if ($exit) {
        exit();
    }
}