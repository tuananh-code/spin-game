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

$date = date('Y-m-d H:m:s');
if (empty($cl['is_logged'])) {
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
} else {
    require_once(cl_full_path("core/apps/game/app_ctrl.php"));
    if ($action == 'load_more') {
        $data['err_code'] = 0;
        $data['status']   = 400;
        $offset           = fetch_or_get($_GET['offset'], 0);
        $type             = fetch_or_get($_GET['type'], false);
        $html_arr         = array();

        $html_arr[] = cl_template('game/includes/list_item');
    } elseif ($action === 'add_game') {
        $event = $_POST['event'];
        $themes = $_POST['themes'];
        $status = $_POST['status'];
        $store_id = $_POST['store_id'];
        if ($_POST['expiresEvent']) {
            $expires_event = str_replace('T', ' ', $_POST['expiresEvent']);
            $expires_event .= ':00';
        } else {
            $expires_event = null;
        }
        $prizes = explode(',', $_POST['prize']);
        $percents = explode(',', $_POST['percent']);
        $stocks = explode(',', $_POST['stock']);
        //handle game
        if ($themes == 'Workout') {
            $layout = [
                "theme" => 'Workout',
                "radius" => '0.84',
                "itemLabelRadius" => '0.93',
                "itemLabelRadiusMax" => '0.35',
                "itemBackgroundColors" => ['#ffc93c', '#66bfbf', '#a2d5f2', '#515070', '#43658b', '#ed6663', '#d54062'],
                "itemLabelColors" => ['#fff'],
                "image" => './themes/default/statics/img/example-0-image.svg',
                "overlayImage" => './themes/default/statics/img/example-0-overlay.svg'
            ];
        } else {
            $layout = [
                "theme" => 'Movies',
                "radius" => '0.88',
                "itemLabelRadius" => '0.92',
                "itemLabelRadiusMax" => '0.4',
                "itemBackgroundColors" => ['#c7160c', '#fff'],
                "itemLabelColors" => ['#fff', '#000'],
                "image" => 'null',
                "overlayImage" => './themes/default/statics/img/example-2-overlay.svg'
            ];
        }
        $themes = json_encode($layout);
        for ($p = 0; $p < count($prizes); $p++) {
            $prize = $prizes[$p];
            $percent = $percents[$p];
            $stock = $stocks[$p];
            $attr[] = [
                'value' => $p,
                'prize' => $prize,
                'percent' => $percent,
                'stock' => $stock
            ];
        }
        $props = json_encode($attr);
        $data = add_game($store_id, $event, $themes, $props, $status, $date, $expires_event);
        //create notify
        $store_id = cl_db_get_items(T_STORE, array('user_id' => $me['id']));
        if ($store_id) {
            foreach ($store_id as $s) {
                $game_id = get_game_id($s['id']);
                foreach ($game_id as $g) {
                    $all = get_all_user_game_id($g);
                    if ($all) {
                        $user[] = $all['user_id'];
                        $notify = cl_db_insert(T_NOTIFS, array(
                            "notifier_id" => $me['id'],
                            "recipient_id" => $all['user_id'],
                            "entry_id" => $me['id'],
                            "status" => '0',
                            "subject" => 'event',
                            "game_id" => $all['game_id'],
                            "json" => 1,
                            "time" => strtotime($date)
                        ));
                    }
                }
            }
            $all_user = implode(',', $user);
            $notify = cl_db_insert(T_NOTIFS, array(
                "notifier_id" => 1,
                "recipient_id" => $me['id'],
                "entry_id" => 1,
                "status" => '0',
                "subject" => 'self',
                "user_id_notify" => $all_user,
                "json" => 1,
                "time" => strtotime($date)
            ));
        }
        return $data;
    } else if ($action == 'add_condition') {
        $event = $_POST['event'];
        $buy = $_POST['buy'];
        $limit = $_POST['limit'];
        $expires = $_POST['expires'];
        $join =  $_POST['join'];
        $store_condition =  $_POST['store_condition'];
        //handle game
        $data = add_condition($event, $store_condition, $buy, $limit, $expires, $join);
        if ($data['status'] == 500) {
            return $data;
        } else {
            $id = cl_db_insert(T_GAME, array(
                "store_id" => $store_condition,
                "game_name" => $event,
                "buy" => $buy,
                "limit" => $limit,
                "expires" => $expires,
                "join" => $join
            ));
            if ($id) {
                $data['status'] = 200;
            } else {
                $data['status'] = 400;
            }
            return $data;
        }
    } else if ($action == 'select_game') {
        $store_id = $_POST['store_id'];
        $data = select_game($store_id);
        return $data;
    } else if ($action == 'add_prize') {
        $prize = $_POST['name'];
        $user = $_POST['user'];
        $store = $_POST['store'];
        $game = $_POST['game'];
        if (@$_POST['keys']) {
            $ticket = $_POST['keys'] - 1;
        }
        $check = true;
        $is_store = cl_db_get_items(T_STORE, array('user_id' => $me['id']));
        if ($is_store) {
            foreach ($is_store as $s) {
                $is_game = cl_db_get_items(T_GAME, array('store_id' => $s['id']));
                if ($is_game) {
                    foreach ($is_game as $g) {
                        if ($g['id'] == $game) {
                            $check = false;
                            break;
                        }
                    }
                }
            }
        }
        if ($check) {
            $update = update_prize($game, $prize);
            $status = cl_db_update(T_GAME, array('store_id' => $store), array('props' => $update));
            if ($_POST['keys']) {
                $status = cl_db_update(T_TICKET, array(
                    'user_id' => $user,
                    'game_id' => $game,
                ), array('ticket' => $ticket));
            }
            $id = cl_db_insert(T_PRIZE, array(
                // "user_id" => $me['id'],
                "prize" => $prize,
                "user_id" => $user,
                "store_id" => $store,
                "game_id" => $game,
                "created_at" => $date
            ));
            if ($id) {
                $data['status'] = 200;
                cl_db_insert(T_NOTIFS, array(
                    "notifier_id" => get_user_id_game($game),
                    "recipient_id" => $me['id'],
                    "entry_id" => get_user_id_game($game),
                    "status" => '0',
                    "subject" => 'prize',
                    "game_id" => $game,
                    "prize_id" => $id,
                    "json" => 1,
                    "attr" => get_store_game_id($game),
                    "time" => strtotime($date)
                ));
            } else {
                $data['status'] = 400;
            }
        } else {
            $data['status'] = 800;
        }
        return $data;
    } elseif ($action == 'publish') {
        $game = $_POST['id'];
        $status = cl_db_update(T_GAME, array('id' => $game), array('status' => 1));
        if ($status) {
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
        }
        return $data;
    }
}
