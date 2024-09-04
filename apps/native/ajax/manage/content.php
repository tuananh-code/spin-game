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
if ($me['admin'] == 0) {
    $data['status'] = 400;
    $data['error']  = 'You do not have access';
} else if ($action == "ccode_exists") {
    $user = cl_get_user_by_code($_POST['ccode']);
    $data['status'] = 200;
    $data['exists'] = 0;
    if ($user) {
        $data['exists'] = 1;
        $data['user'] = $user;
    }
} else if ($action == "create_invoice") {
    $data['err_code'] =  0;
    $data['status']   =  400;
    $user = ($_POST['ccode'] || $_POST['phone']) ? cl_get_user_by_code($_POST['ccode'], $_POST['phone']) : null;
    $user_id = $user ? $user["id"] : null;
    $send_amount      = fetch_or_get($_POST['points'], 0);
    $amount = $_POST['points'] ? str_replace(',', '', $_POST['amount']) : 0;
    $cname = fetch_or_get($_POST['cname'], null);
    $qty = fetch_or_get($_POST['qty'], 0);
    if ($user_id == $me["id"]) {
        $data['err_code'] =  "yourself";
    }
    $transaction_data_fields =  array(
        'cname'       => $cname,
        'pname'       => fetch_or_get($_POST['pname'], null),
        'business_id'       => $me["id"],
        'customer_id'       => $user_id,
        'phone'       => fetch_or_get($_POST['phone'], null),
        'ccode'       => fetch_or_get($_POST['ccode'], null),
        'points'       => $send_amount,
        'pcode'       => fetch_or_get($_POST['pcode'], null),
        'qty'       => $qty,
        'email'       => fetch_or_get($_POST['email'], null),
        'created_at'       => fetch_or_get($_POST['time'], null),
        'amount'       => $amount,
        'weight'       => fetch_or_get($_POST['weight'], null)
    );
    foreach ($transaction_data_fields as $field_name => $field_val) {
        if ($field_name == 'ccode') {
            if (empty($field_val)) {
                // $data['err_code'] = "invalid_ccode"; break;
            } else if (len_between($field_val, 10, 25) != true) {
                $data['err_code'] = "invalid_ccode";
                break;
            }
        } else if ($field_name == 'phone') {
            if (empty($field_val) || len_between($field_val, 3, 25) != true) {
                $data['err_code'] = "invalid_phone";
                break;
            }
        } else if ($field_name == 'phone') {
            if (empty($field_val) || len_between($field_val, 3, 25) != true) {
                $data['err_code'] = "invalid_phone";
                break;
            }
        } else if ($field_name == 'points' && $field_val != 0) {
            if (empty($field_val) || !is_numeric($field_val)) {
                $data['err_code'] = "invalid_points";
                break;
            }

            if ($field_val > $send_amount) {
                $data['err_code'] = "lack_points";
                break;
            }
        } else if ($field_name == 'amount') {


            if ((empty($field_val) && ($field_val != 0) || !is_numeric($field_val))) {
                $data['err_code'] = "invalid_amount";
                break;
            }
        }
    }



    if (empty($data['err_code'])) {
        $data['status'] = 200;

        cl_db_insert('cl_transaction', $transaction_data_fields);
        // Calculate points transferred or received
        if (is_numeric($send_amount)) {

            if (is_posnum($user_id)) {
                $recipient_data = cl_raw_user_data($user_id);
                if (not_empty($recipient_data)) {

                    $trans_id = cl_strf("TID_%s", sha1(microtime()));

                    cl_update_user_data($user_id, array(
                        "wallet" => ($recipient_data["wallet"] += $send_amount)
                    ));

                    cl_update_user_data($me["id"], array(
                        "wallet" => ($me["wallet"] -= $send_amount)
                    ));

                    $db->insert(T_WALLET_HISTORY, array(
                        "user_id" => $me["id"],
                        "operation" => "wallet_local_transfer",
                        "amount" => $send_amount,
                        "time" => time(),
                        "status" => "success",
                        "trans_id" => $trans_id,
                        "json_data" => cl_minify_js(json(array("username" => cl_strf("%s %s", $recipient_data["fname"], $recipient_data["lname"])), true))
                    ));

                    $db->insert(T_WALLET_HISTORY, array(
                        "user_id" => $user_id,
                        "operation" => "wallet_local_receipt",
                        "amount" => $send_amount,
                        "time" => time(),
                        "status" => "success",
                        "trans_id" => $trans_id,
                        "json_data" => cl_minify_js(json(array("username" => $me["name"]), true))
                    ));

                    cl_notify_user(array(
                        'subject'  => 'wallet_local_receipt',
                        'user_id'  => $recipient_data["id"],
                        'entry_id' => $me["id"],
                        'json' => cl_minify_js(json(array(
                            "trans_amount" => cl_money($send_amount)
                        ), true))
                    ));
                }
            } else {
                $trans_id = cl_strf("TID_%s", sha1(microtime()));

                // cl_update_user_data($user_id, array(
                //     "wallet" => ($recipient_data["wallet"] += $send_amount)
                // ));

                cl_update_user_data($me["id"], array(
                    "wallet" => ($me["wallet"] -= $send_amount)
                ));

                $db->insert(T_WALLET_HISTORY, array(
                    "user_id" => $me["id"],
                    "operation" => "wallet_local_transfer",
                    "amount" => $send_amount,
                    "time" => time(),
                    "status" => "success",
                    "trans_id" => $trans_id,
                    "json_data" => cl_minify_js(json(array("username" => $cname), true))
                ));

                $db->insert(T_WALLET_HISTORY, array(
                    "user_id" => 0,
                    "operation" => "wallet_local_receipt",
                    "amount" => $send_amount,
                    "time" => time(),
                    "status" => "success",
                    "trans_id" => $trans_id,
                    "json_data" => cl_minify_js(json(array("username" => $me["name"]), true))
                ));

                // cl_notify_user(array(
                //     'subject'  => 'wallet_local_receipt',
                //     'user_id'  => $recipient_data["id"],
                //     'entry_id' => $me["id"],
                //     'json' => cl_minify_js(json(array(
                //         "trans_amount" => cl_money($send_amount)
                //     ), true))
                // ));
            }
        }
        $game_data = get_game_attr($_POST['game_id']);

        if ($game_data) {
            $id = $user_id;

            $buy = $game_data['buy'];
            $limit = $game_data['limit'];
            $expires = $game_data['expires'];
            $join = $game_data['join'];
            $created_at = $game_data['created_at'];

            if ($expires) {
                $expires = '+' . $game_data['expires'] . ' days';
                $date = new DateTime($_POST['time']);

                $expires_date = $date->modify($expires);
                $expires_date = $date->format('Y-m-d H:i:s');
            } else {
                $expires_date = null;
            }

            //ticket
            $post_date = date('Y-m-d H:m:s');
            $ticket = floor($amount / $limit);
            $ticket_field = [
                'user_id' => $id,
                'game_id' => fetch_or_get($_POST['game_id'], null),
                'ticket' => $ticket,
                'created_at' => fetch_or_get($_POST['time'], null),
                'expires_date' => $expires_date,
            ];


            cl_db_insert(T_NOTIFS, array(
                "notifier_id" => $me['id'],
                "recipient_id" => $id,
                "entry_id" => $me['id'],
                "status" => '0',
                "subject" => 'buy',
                "game_id" => $_POST['game_id'],
                "json" => 1,
                "attr" => $_POST['pname'],
                "time" => strtotime($post_date)
            ));
            $mess = cl_translate('You buy ') . '<b>' . $_POST['pname'] . '</b>' . cl_translate(' at store ') . '<b>' . get_store_name_game($_POST['game_id']) . '</b>';
            cl_db_insert(T_MSGS, array(
                'sent_by' => $me['id'],
                'sent_to' => $id,
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
            // ticket
            $check = cl_db_get_item(T_TICKET, array(
                'user_id' => $id,
                'game_id' => $_POST['game_id']
            ));
            if ($check) {
                $total = $ticket + $check['ticket'];
                if ($join == 0) {
                    cl_db_update(T_TICKET, array('id' => $check['id']), array(
                        'ticket' => $total,
                        'created_at' => fetch_or_get($_POST['time'], null),
                        'expires_date' => $expires_date,
                    ));
                    if ($ticket > 0) {
                        cl_db_insert(T_NOTIFS, array(
                            "notifier_id" => $me['id'],
                            "recipient_id" => $id,
                            "entry_id" => $me['id'],
                            "status" => '0',
                            "subject" => 'ticket',
                            "game_id" => $_POST['game_id'],
                            "json" => 1,
                            "attr" => $ticket,
                            "time" => strtotime($post_date)
                        ));
                        //
                        cl_db_insert(T_NOTIFS, array(
                            "notifier_id" => 1,
                            "recipient_id" => $me['id'],
                            "entry_id" => 1,
                            "status" => '0',
                            "subject" => 'self_ticket',
                            "game_id" => $_POST['game_id'],
                            "json" => 1,
                            "attr" =>  $id . ',' . $ticket,
                            "time" => strtotime($post_date)
                        ));
                        $mess = cl_translate('Congratulation. You receive ') . '<b>' . $ticket . '</b>' . cl_translate(' from store ') . '<b>' . get_store_name_game($_POST['game_id']) . '</b>' . cl_translate('. Spin now');
                        cl_db_insert(T_MSGS, array(
                            'sent_by' => $me['id'],
                            'sent_to' => $id,
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
                } else if ($join <= $total) {
                    $get_total = $total; // Default value
                    for ($t = 0; $t < $total; $t++) {
                        $new_total = $total - $t;
                        if ($new_total <= $join) {
                            $get_total = $new_total;
                            break;
                        }
                    }
                    cl_db_update(T_TICKET, array('id' => $check['id']), array(
                        'ticket' => $get_total,
                        'created_at' => fetch_or_get($_POST['time'], null),
                        'expires_date' => $expires_date,
                    ));
                    if ($t > 0) {
                        cl_db_insert(T_NOTIFS, array(
                            "notifier_id" => $me['id'],
                            "recipient_id" => $id,
                            "entry_id" => $me['id'],
                            "status" => '0',
                            "subject" => 'ticket',
                            "game_id" => $_POST['game_id'],
                            "json" => 1,
                            "attr" => $t,
                            "time" => strtotime($post_date)
                        ));
                        //self
                        cl_db_insert(T_NOTIFS, array(
                            "notifier_id" => 1,
                            "recipient_id" => $me['id'],
                            "entry_id" => 1,
                            "status" => '0',
                            "subject" => 'self_ticket',
                            "game_id" => $_POST['game_id'],
                            "json" => 1,
                            "attr" => $id . ',' . $t,
                            "time" => strtotime($post_date)
                        ));
                        //msg
                        $mess = cl_translate('Congratulation. You receive ') . '<b>' . $t . '</b>' . cl_translate(' ticket from store ') . '<b>' . get_store_name_game($_POST['game_id']) . '</b>' . cl_translate('. Spin now');
                        cl_db_insert(T_MSGS, array(
                            'sent_by' => $me['id'],
                            'sent_to' => $id,
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
                } else {
                    cl_db_update(T_TICKET, array('id' => $check['id']), array(
                        'ticket' => $total,
                        'created_at' => fetch_or_get($_POST['time'], null),
                        'expires_date' => $expires_date,
                    ));
                    if ($ticket > 0) {
                        cl_db_insert(T_NOTIFS, array(
                            "notifier_id" => $me['id'],
                            "recipient_id" => $id,
                            "entry_id" => $me['id'],
                            "status" => '0',
                            "subject" => 'ticket',
                            "game_id" => $_POST['game_id'],
                            "json" => 1,
                            "attr" => $ticket,
                            "time" => strtotime($post_date)
                        ));
                        //self
                        cl_db_insert(T_NOTIFS, array(
                            "notifier_id" => 1,
                            "recipient_id" => $me['id'],
                            "entry_id" => 1,
                            "status" => '0',
                            "subject" => 'self_ticket',
                            "game_id" => $_POST['game_id'],
                            "json" => 1,
                            "attr" =>  $id . ',' . $ticket,
                            "time" => strtotime($post_date)
                        ));
                        $mess = cl_translate('Congratulation. You receive ') . '<b>' . $ticket . '</b>' . cl_translate(' from store ') . '<b>' . get_store_name_game($_POST['game_id']) . '</b>' . cl_translate('. Spin now');
                        cl_db_insert(T_MSGS, array(
                            'sent_by' => $me['id'],
                            'sent_to' => $id,
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
                cl_db_insert('cl_ticket', $ticket_field);
                if ($ticket > 0) {
                    cl_db_insert(T_NOTIFS, array(
                        "notifier_id" => $me['id'],
                        "recipient_id" => $id,
                        "entry_id" => $me['id'],
                        "status" => '0',
                        "subject" => 'ticket',
                        "game_id" => $_POST['game_id'],
                        "json" => 1,
                        "attr" => $ticket,
                        "time" => strtotime($post_date)
                    ));

                    $mess = cl_translate('Congratulation. You receive ') . '<b>' . $ticket . '</b>' . cl_translate(' from store ') . '<b>' . get_store_name_game($_POST['game_id']) . '</b>' . cl_translate('. Spin now');
                    cl_db_insert(T_MSGS, array(
                        'sent_by' => $me['id'],
                        'sent_to' => $id,
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
        }
    }
} else if ($action == "save_invoice") {
    $data['err_code'] =  0;
    $data['status']   =  400;
    $user = ($_POST['ccode'] || $_POST['phone']) ? cl_get_user_by_code($_POST['ccode'], $_POST['phone']) : null;
    $id = fetch_or_get($_POST['id'], null);
    $transaction_data_fields =  array(
        'cname'       => fetch_or_get($_POST['cname'], null),
        'pname'       => fetch_or_get($_POST['pname'], null),
        'business_id'       => $me["id"],
        'customer_id'       => $user ? $user["id"] : null,
        'phone'       => fetch_or_get($_POST['phone'], null),
        'ccode'       => fetch_or_get($_POST['ccode'], null),
        'pcode'       => fetch_or_get($_POST['pcode'], null),
        'qty'       => fetch_or_get($_POST['qty'], null),
        'email'       => fetch_or_get($_POST['email'], null),
        'updated_at'       => fetch_or_get($_POST['time'], null),
        'amount'       => str_replace(',', '', $_POST['amount']),
        'weight'       => fetch_or_get($_POST['weight'], null)
    );

    foreach ($transaction_data_fields as $field_name => $field_val) {
        if ($field_name == 'code') {
            if (empty($field_val)) {
                // $data['err_code'] = "invalid_ccode"; break;
            } else if (len_between($field_val, 10, 25) != true) {
                $data['err_code'] = "invalid_ccode";
                break;
            }

            // else if (preg_match('/^[\w]+$/', $field_val) != true) {
            //     $data['err_code'] = "invalid_uname"; break;
            // }

            // else if(cl_uname_exists($field_val) && $field_val != $me['raw_uname']) {
            //     $data['err_code'] = "doubling_uname"; break;
            // }

            // else if(in_array($field_val, $username_restricts) && $field_val != $me['raw_uname']) {
            //     $data['err_code'] = "denied_uname"; break;
            // }
        } else if ($field_name == 'phone') {
            if (empty($field_val) || len_between($field_val, 3, 25) != true) {
                $data['err_code'] = "invalid_phone";
                break;
            }
        } else if ($field_name == 'amount') {

            if ((empty($field_val) && ($field_val != 0) || !is_numeric($field_val))) {
                $data['err_code'] = "invalid_amount";
                break;
            }
        }
    }

    if (empty($data['err_code'])) {
        $data['status'] = 200;
        cl_db_update('cl_transaction', array(
            "id" => $id
        ), $transaction_data_fields);
    }
} else if ($action == "delete") {
    $data['status']   = 400;
    $id = fetch_or_get($_POST['id'], null);
    $data['err_code'] = $id ? 0 : 1;

    if (empty($data['err_code'])) {
        $data['status'] = 200;
        cl_db_update('cl_transaction', array(
            "id" => $id
        ), array(
            "del" => '1'
        ));
    }
} else if ($action == "load_more") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $offset           = fetch_or_get($_GET['offset'], 0);
    $type             = fetch_or_get($_GET['type'], false);
    $notifs_list      = array();
    $html_arr         = array();

    if (is_posnum($offset) && in_array($type, array('list'))) {

        require_once(cl_full_path("core/apps/manage/app_ctrl.php"));

        $notifs_list =  cl_get_transactions(array(
            "type"   => $type,
            "offset" => $offset,
            "limit"  => 50,
        ));

        if (not_empty($notifs_list)) {
            foreach ($notifs_list as $cl['li']) {
                $html_arr[] = cl_template('manage/includes/list_item');
            }

            $data['status'] = 200;
            $data['html']   = implode("", $html_arr);
        }
    }
}
