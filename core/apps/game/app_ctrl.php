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

function cl_get_game($args = array())
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

    return $args;
}
// var_dump(cl_get_game());
function add_game($id, $event)
{

    global $db, $cl, $me;
    // $id = $me['id'];
    // var_dump($get_store);
    $data = array();
    $sql_store = cl_sqltepmlate("apps/game/sql/game", array(
        't_store' => T_STORE,
        'user_id' => $id,
    ));
    $sql_store = $db->rawQuery($sql_store);
    if (cl_queryset($sql_store)) {
        foreach ($sql_store as $row) {
            $sql_exist = cl_sqltepmlate("apps/game/sql/exist", array(
                't_game' => T_GAME,
                'store_id' => $row['id'],
                'game' => $event,
            ));
            $exist = $db->rawQuery($sql_exist);
            if (cl_queryset($exist)) {
                $check = true;
            } else {
                $data['status'] = 200;
                return $data;
            }
        }
        if ($check) {
            $data['status'] = 500;
            return $data;
        }
    }
}
function add_condition($store_condition, $buy, $limit, $quantity, $expires, $join)
{
    global $db, $cl, $me;
    // $id = $me['id'];
    // var_dump($get_store);
    $data = array();
    $sql_condition = cl_sqltepmlate("apps/game/sql/update", array(
        't_game' => T_GAME,
        'store_condition' => $store_condition,
        'buy' => $buy,
        'limit' => $limit,
        'quantity' => $quantity,
        'expires' => $expires,
        'join' => $join,
    ));
    $sql_condition = $db->rawQuery($sql_condition);
    if (!cl_queryset($sql_condition)) {
        $data['status'] = 200;
        return $data;
    }
}
function select_game($store_id)
{
    global $db, $cl, $me;
    // $id = $me['id'];
    // var_dump($get_store);
    $data = array();
    $sql_condition = cl_sqltepmlate("apps/game/sql/select_game", array(
        't_game' => T_GAME,
        'store_id' => $store_id,
    ));
    $sql_condition = $db->rawQuery($sql_condition);
    if (cl_queryset($sql_condition)) {
        foreach ($sql_condition as $row) {
            $themes = $row['themes'];
            $props = $row['props'];
            $data = [
                'themes' => $themes,
                'props' => $props
            ];
        }
        return $data;
    }
}
