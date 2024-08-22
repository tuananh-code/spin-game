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
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// global $db, $cl, $me;
// function get_id($me)
// {
//     $id = $me['id'];
//     return $id;
// }
function cl_get_managed($args = array())
{
    global $db, $cl, $me;
    $args        = (is_array($args)) ? $args : array();
    $options     = array(
        "offset" => false,
        "limit"  => 10,
        "type"   => "notifs",
        "id" => $me['id']
    );

    $args   = array_merge($options, $args);
    $offset = $args['offset'];
    $limit  = $args['limit'];
    $type   = $args['type'];
}
function add_store($id, $store, $address, $city, $phone, $mail)
{
    global $db, $cl, $me;
    // $id = $me['id'];
    // var_dump($get_store);
    $data = array();
    $sql_exist = cl_sqltepmlate("apps/store/sql/exist", array(
        't_store' => T_STORE,
        'store' => $store,
        'address' => $address,
        'city' => $city,
        'phone' => $phone,
        'mail' => $mail,
    ));
    $exist = $db->rawQuery($sql_exist);
    if (cl_queryset($exist)) {
        $data['status'] = 500;
        return $data;
    } else {
        $data['status'] = 200;
        return $data;
    }

    // return $args;
}