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
        $prizes = explode(',', $_POST['prize']);
        $percents = explode(',', $_POST['percent']);
        $stocks = explode(',', $_POST['stock']);
        //handle game
        $data = add_game($me['id'], $event);
        if ($data['status'] == 500) {
            return $data;
        } else {
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

            $id = cl_db_insert(T_GAME, array(
                // "user_id" => $me['id'],
                "game_name" => $event,
                "themes" => $themes,
                "props" => $props,
                "status" => $status,
                "store_id" => $store_id,
                "created_at" => $date
            ));
            if ($id) {
                $data['status'] = 200;
            } else {
                $data['status'] = 400;
            }
            return $data;
        }
    } else if ($action == 'add_condition') {
        $buy = $_POST['buy'];
        $limit = $_POST['limit'];
        $quantity = $_POST['quantity'];
        $expires = $_POST['expires'];
        $join =  $_POST['join'];
        $store_condition =  $_POST['store_condition'];
        //handle game
        $data = add_condition($store_condition, $buy, $limit, $quantity, $expires, $join);
        return $data;
    } else if ($action == 'select_game') {
        $store_id = $_POST['store_id'];
        $data = select_game($store_id);
        return $data;
    } else if ($action == 'add_prize') {
        $prize = $_POST['name'];
        $user = $_POST['user'];
        $store = $_POST['store'];
        $game = $_POST['game'];
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
        } else {
            $data['status'] = 400;
        }
        return $data;
    }
}
