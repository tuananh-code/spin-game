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

$post_date = date('Y-m-d H:m:s');
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
        $data = add_game($store_id, $event, $themes, $props, $status, $post_date, $expires_event);
        $game_id_noti = cl_db_get_item(T_GAME, array(
            'store_id' => $store_id,
            'game_name' => $event
        ));
        //create notify
        $store_id = cl_db_get_items(T_STORE, array('user_id' => $me['id']));
        if ($store_id) {
            foreach ($store_id as $s) {
                $game_id = get_game_id($s['id']);
                foreach ($game_id as $g) {
                    $all = get_all_user_game_id($g);
                    if ($all && $status !== 0) {
                        if ($all['user_id']) {
                            // var_dump($all);
                            @$mailbox = get_user_data($me['id'])['email'];
                            @$mail = get_user_data($all['user_id'])['email'];
                            @$mail_pass = get_user_data($all['user_id'])['smtp_mail'];

                            $body = '<center style="background: antiquewhite; padding: 2em; border-radius: 40px; margin: 0 auto; max-width: 600px;"><h2 style="color:black;">We create new event with store: ' . '<b style="color: blue; border-radius: 20px;">' . get_store_name_game($game_id_noti['id']) . '</b></h2>' . '<h3><a style="padding: 1em; margin: 1em; background: green; color: white; border-radius: 50px; text-decoration:none; width:200px; display:block" href="https://mycheery.com">Check it now</a></h3></center>';
                            if (@$mailbox && @$mail && @$mail_pass) {
                                $send = send_mail($mailbox, $mail_pass, $mail, $body);
                                if ($send) {
                                    $data['mail'] = true;
                                } else {
                                    $data['mail'] = false;
                                }
                            } else {
                                $data['mail'] = false;
                            }
                            $user[] = $all['user_id'];
                            $check_notify = cl_db_get_item(T_NOTIFS, array(
                                'notifier_id' => $me['id'],
                                'recipient_id' => $all['user_id'],
                                'game_id' => $game_id_noti['id'],
                            ));
                            if (!$check_notify) {
                                $notify = cl_db_insert(T_NOTIFS, array(
                                    "notifier_id" => $me['id'],
                                    "recipient_id" => $all['user_id'],
                                    "entry_id" => $me['id'],
                                    "status" => '0',
                                    "subject" => 'event',
                                    "game_id" => $game_id_noti['id'],
                                    "json" => 1,
                                    "time" => strtotime($post_date)
                                ));
                            }
                            $mess = cl_translate('We create new event with ') . '<b>' . get_store_name_game($game_id_noti['id']) . '</b>' . cl_translate('. Join now');
                            $check_mess = cl_db_get_item(T_MSGS, array(
                                'sent_by' => $me['id'],
                                'sent_to' => $all['user_id'],
                                'message' => $mess,
                            ));
                            if (!$check_mess) {
                                $msg = cl_db_insert(T_MSGS, array(
                                    'sent_by' => $me['id'],
                                    'sent_to' => $all['user_id'],
                                    'owner' => $me['id'],
                                    'message' => $mess,
                                    'media_file' => '',
                                    'audio_record' => '',
                                    'media_type' => 'none',
                                    'seen' => 0,
                                    'deleted_fs1' => 'N',
                                    'deleted_fs2' => 'N',
                                    'time' => strtotime($post_date)
                                ));
                            }
                        }
                    } else {
                        $user = null;
                    }
                }
            }
            if ($user) {
                $all_user = implode(',', $user);
                $notify = cl_db_insert(T_NOTIFS, array(
                    "notifier_id" => 1,
                    "recipient_id" => $me['id'],
                    "entry_id" => 1,
                    "status" => '0',
                    "subject" => 'self',
                    "user_id_notify" => $all_user,
                    "json" => 1,
                    "time" => strtotime($post_date)
                ));
            }
        }
        foreach ($prizes as $pr) {
            $all[] = "<b class='p-2 m-2 text-success'>- $pr.</b>";
        }
        $store_ids = $_POST['store_id'];
        $all_prize = implode('<br>', $all);
        //trans
        $create = cl_translate('We create new event');
        $with = cl_translate('with');
        $prize_list = cl_translate('Prize list');
        $contact = cl_translate('Contact us to come and join now');
        $rule = cl_translate('Event rules');
        $at_least = cl_translate('Buy at least');
        $get_ticket = cl_translate('to get ticket.');
        $notice = cl_translate('Note: Event end at');
        $parti = cl_translate('Participate Event');
        $cnow = cl_translate('Contact now');
        $buy = get_game_buy($game_id_noti['id']);
        $exp_event = $_POST['expiresEvent'];
        $username = cl_link(get_user_by_id($me['id']));
        $href = cl_link('spin_prize');
        //
        $text = "<div><span>$create <b class='text-primary'>" . $event . "</b> $with <b class='text-primary'>" . get_store_name($store_ids) . "</b>.</span><br> <span>+ $prize_list:</span> <br> <div>$all_prize</div></div><div><div><b class='text-primary'>** $rule:</b></div><div><span>+ $at_least: <b>$buy</b> $get_ticket</span></div><div><small class='text-danger'>** $notice: $exp_event.</small></div></div><div class='d-flex align-items-center'><b class='pe-2 text-success'>Join Here: </b><b><a class='text-primary' href='$href'>$parti</a></b></div><div><b>$contact. <a class='text-primary' href='$username'> $cnow</a></b></div>";
        $post = cl_db_insert(T_PUBS, array(
            'user_id' => $me['id'],
            'text' => $text,
            'time' => time()
        ));
        $add = cl_db_insert(T_POSTS, array(
            'user_id' => $me['id'],
            'publication_id' => $post,
            'time' => time()
        ));
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
                "created_at" => $post_date
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
                    "time" => strtotime($post_date)
                ));
                cl_db_insert(T_NOTIFS, array(
                    "notifier_id" => 1,
                    "recipient_id" => get_user_id_game($game),
                    "entry_id" => 1,
                    "status" => '0',
                    "subject" => 'self_prize',
                    "game_id" => $game,
                    "prize_id" => $id,
                    "json" => 1,
                    "attr" => $me['id'],
                    "time" => strtotime($post_date)
                ));
                $mess = cl_translate('Congratulation. You receive ') . '<b>' . get_prize_name($id) . '</b>' . cl_translate(' from event ') . '<b>' . get_game_name($game) . '</b>' . cl_translate(' at store ') . '<b>' . get_store_name_game($game) . '</b>';

                $msg = cl_db_insert(T_MSGS, array(
                    'sent_by' => get_user_id_game($game),
                    'sent_to' => $me['id'],
                    'owner' => get_user_id_game($game),
                    'message' => $mess,
                    'media_file' => '',
                    'audio_record' => '',
                    'media_type' => 'none',
                    'seen' => 0,
                    'deleted_fs1' => 'N',
                    'deleted_fs2' => 'N',
                    'time' => strtotime($post_date)
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
    } elseif ($action == 'check_ticket') {
        $phone = $_POST['phone'];
        $phone_check = cl_db_get_item(T_USERS, array('phone' => $phone));
        if ($phone_check) {
            $data['status'] = 500;
            // var_dump($data);die;
            return $data;
        }
        $ph = cl_db_update(T_USERS, array('id' => $me['id'],), array('phone' => $phone));
        cl_db_update(T_TRANSACTION, array('phone' => $phone), array('customer_id' => $me['id']));
        if ($ph) {
            $data['status'] = 200;
        }
        //trans
        $trans_data = cl_db_get_item(T_TRANSACTION, array(
            'phone' => $phone,
        ));
        if ($trans_data) {
            $customer_id = $trans_data['customer_id'];
            $trans_id = $trans_data['id'];
            $pname = $trans_data['pname'];
            $amount = $trans_data['amount'];
            $qty = $trans_data['qty'];
            $data['phone'] = 200;
        }

        //ticket
        $ticket_data = cl_db_get_item(T_TICKET, array(
            'phone' => $phone
        ));
        if ($ticket_data) {
            $data['ticket'] = 200;
            $ticket_id = $ticket_data['id'];
            $game_id = $ticket_data['game_id'];
            $ticket = $ticket_data['ticket'];
            $expires_date = $ticket_data['expires_date'];
            $owner = get_user_id_game($game_id);
            cl_db_update(T_TICKET, array('id' => $ticket_id,), array('user_id' => $customer_id));
            // noti & msg
            cl_db_insert(T_NOTIFS, array(
                "notifier_id" => $owner,
                "recipient_id" => $me['id'],
                "entry_id" => $owner,
                "status" => '0',
                "subject" => 'buy',
                "game_id" => $game_id,
                "json" => 1,
                "attr" => $pname,
                "time" => strtotime($post_date)
            ));
            $mess = cl_translate('You buy ') . '<b>' . $pname . '</b>' . cl_translate(' at store ') . '<b>' . get_store_name_game($game_id) . '</b>';
            cl_db_insert(T_CHATS, array(
                'user_one' => $owner,
                'user_two' => $me['id'],
                'time' => strtotime($post_date)
            ));
            cl_db_insert(T_CHATS, array(
                'user_one' => $me['id'],
                'user_two' => $owner,
                'time' => strtotime($post_date)
            ));
            cl_db_insert(T_MSGS, array(
                'sent_by' => $owner,
                'sent_to' => $me['id'],
                'owner' => $owner,
                'message' => $mess,
                'media_file' => '',
                'audio_record' => '',
                'media_type' => 'none',
                'seen' => 0,
                'deleted_fs1' => 'N',
                'deleted_fs2' => 'N',
                'time' => strtotime($post_date)
            ));
            if ($ticket > 0) {
                cl_db_insert(T_NOTIFS, array(
                    "notifier_id" => $owner,
                    "recipient_id" => $me['id'],
                    "entry_id" => $owner,
                    "status" => '0',
                    "subject" => 'ticket',
                    "game_id" => $game_id,
                    "json" => 1,
                    "attr" => $ticket,
                    "time" => strtotime($post_date)
                ));
                //self
                cl_db_insert(T_NOTIFS, array(
                    "notifier_id" => 1,
                    "recipient_id" => $owner,
                    "entry_id" => 1,
                    "status" => '0',
                    "subject" => 'self_ticket',
                    "game_id" => $game_id,
                    "json" => 1,
                    "attr" => $customer_id . ',' . $ticket,
                    "time" => strtotime($post_date)
                ));
                //msg
                $mess = cl_translate('Congratulation. You receive ') . '<b>' . $ticket . '</b>' . cl_translate(' ticket from store ') . '<b>' . get_store_name_game($game_id) . '</b>' . cl_translate('. Spin now');
                cl_db_insert(T_MSGS, array(
                    'sent_by' => $owner,
                    'sent_to' => $me['id'],
                    'owner' => $owner,
                    'message' => $mess,
                    'media_file' => '',
                    'audio_record' => '',
                    'media_type' => 'none',
                    'seen' => 0,
                    'deleted_fs1' => 'N',
                    'deleted_fs2' => 'N',
                    'time' => strtotime($post_date)
                ));
            }
        }
        return $data;
    }
}
