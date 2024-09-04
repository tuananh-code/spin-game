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
}

else if ($action == "cread_invoice") {
	$data['err_code'] =  0;
    $data['status']   =  400;
    $user = ($_POST['ccode'] || $_POST['phone']) ? cl_get_user_by_code($_POST['ccode'], $_POST['phone']) : null;
    $user_id = $user ? $user["id"] : null;
    $send_amount      = fetch_or_get($_POST['points'], null);
    $cname= fetch_or_get($_POST['cname'], null);
    if ($user_id == $me["id"]) {
        $data['err_code'] =  "yourself";
    }
    $transaction_data_fields =  array(
        'cname'       => $cname,
        'pname'       => fetch_or_get($_POST['pname'], null),
        'business_id'       => $me["id"],
        'customer_id'       => $user_id,
        'phone'       => fetch_or_get($_POST['phone'],null),
        'ccode'       => fetch_or_get($_POST['ccode'], null),
        'points'       => $send_amount,
        'pcode'       => fetch_or_get($_POST['pcode'], null),
        'qty'       => fetch_or_get($_POST['qty'], null),
        'email'       => fetch_or_get($_POST['email'], null),
        'created_at'       => fetch_or_get($_POST['time'], null),
        'amount'       => str_replace(',', '', $_POST['amount']),
        'weight'       => fetch_or_get($_POST['weight'],null)
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
        }

		else if ($field_name == 'phone') {
			if (empty($field_val) || len_between($field_val,3,25) != true) {
	            $data['err_code'] = "invalid_phone"; break;
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
    //condition
    $username = fetch_or_get($_POST['username'], null);
    $amount = fetch_or_get($_POST['amount'], null);
    $user_id = cl_db_get_item(T_USERS, array('username' => $username));
    $id = $user_id['id'];
    $game_data = get_game_attr($_POST['game_id']);


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

	if (empty($data['err_code'])) {
        $data['status'] = 200;
        cl_db_insert('cl_transaction', $transaction_data_fields);
    }
} 

else if ($action == "save_invoice") {
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
}
else if ($action == "delete") {
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
