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
function add_game($id, $event, $themes, $props, $status, $date, $expires_event)
{

    global $db, $cl, $me;

    $data = array();

    $sql_game = cl_sqltepmlate("apps/game/sql/update", array(
        't_game' => T_GAME,
        'store_id' => $id,
        'event' => $event,
        'themes' => $themes,
        'props' => $props,
        'status' => $status,
        'expires_event' => $expires_event,
    ));
    $sql_game = $db->rawQuery($sql_game);
    if (!cl_queryset($sql_game)) {
        $data['status'] = 200;
        return $data;
    }
}
function add_condition($event, $store_condition, $buy, $limit, $quantity, $expires, $join)
{
    global $db, $cl, $me;
    // $id = $me['id'];
    // var_dump($get_store);
    $data = array();
    $sql_exist = cl_sqltepmlate("apps/game/sql/exist", array(
        't_game' => T_GAME,
        'store_id' => $store_condition,
        'game' => $event,
    ));
    $exist = $db->rawQuery($sql_exist);
    if (cl_queryset($exist)) {
        $check = true;
    } else {
        $data['status'] = 200;
        return $data;
    }
    if ($check) {
        $data['status'] = 500;
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
function update_prize($game, $prize)
{
    global $db, $cl, $me;
    $sql_prop = cl_sqltepmlate("apps/game/sql/select_prop", array(
        't_game' => T_GAME,
        'game_id' => $game,
    ));
    $sql_prop = $db->rawQuery($sql_prop);
    if (cl_queryset($sql_prop)) {
        foreach ($sql_prop as $row) {
            $props = $row['props'];
        }
    }
    $prop = json_decode($props, true);
    foreach ($prop as $key => &$p) { // Use & to modify the array by reference
        if ($prize == $p['prize']) {
            if ($p['stock'] == 1) {
                // Remove the element from the array
                unset($prop[$key]);
                // Find the largest percent (only run if a prize was removed)
                $largestPercent = 0;
                $largestPercentKey = null;
                foreach ($prop as $k => $item) {
                    if ((int)$item['percent'] > $largestPercent) {
                        $largestPercent = (int)$item['percent'];
                        $largestPercentKey = $k;
                    }
                }
                if ($largestPercentKey !== null) {
                    $prop[$largestPercentKey]['percent'] = (int)$prop[$largestPercentKey]['percent'] + (int)$p['percent'];
                }
            } else {
                // Update the stock
                $p['stock'] = $p['stock'] - 1;
            }
            break; // Exit the loop after finding the prize
        }
    }
    $newProps = [];
    $value = 0;

    foreach ($prop as $item) {
        $item['value'] = $value;
        $newProps[] = $item;
        $value++;
    }
    $props = json_encode($newProps);
    return $props;
    // die;
}
