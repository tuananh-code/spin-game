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
} else if ($action == "get_system") {
    $data['status'] = 200;
    $data['system'] = null;
    $store_id = fetch_or_get($_POST['store_id'], null);
    $info_data_fields = cl_db_get_item(T_POINTS_INFO, array(
        'store_id' => $store_id,
        'owner_id' => $me["id"]
    ));

    if (!$info_data_fields) {
        return;
    }
    $distri = cl_db_get_item(T_POINTS_DISTRI, array(
        'point_id' => $info_data_fields['id'],
        'user_id' => $me["id"]
    ));
    foreach ($info_data_fields as $key => $value) {
        if ($key === 'created_at' || $key === 'updated_at' ) {
            unset($info_data_fields[$key]);
        } elseif ($key === 'process') {
            $info_data_fields[$key] = intval($value);
        } elseif ($value === null) {
            $info_data_fields[$key] = '';
        }

    }
    $info_data_fields['rest_point'] = $distri["rewards"];
    $data['system'] = $info_data_fields;
} else if ($action == "create_point") {
    $data['err_code'] =  0;
    $data['status']   =  400;
    $store_id = fetch_or_get($_POST['store'], null);
    $check_store_id = $store_id ? cl_check_store($me["id"], $store_id) : false;
    $paid = fetch_or_get($_POST['paid'], null) ? str_replace(',', '', $_POST['paid']) : 0;
    $name = fetch_or_get($_POST['name'], null);
    $qty = fetch_or_get($_POST['qty'], 0);
    $items = fetch_or_get($_POST['items'], 0);
    $rewards = fetch_or_get($_POST['rewards'], null);

    $limit_store_id =  $check_store_id ? $store_id  : null;
    $limit_time_begin = formattedDateTime($_POST['begin'], null);
    $limit_time_end = formattedDateTime($_POST['end'], null);
    $limit_pcodes =  fetch_or_get($_POST['pcode_array'], null);
    $meet_1 = fetch_or_get($_POST['meet_1'], null);
    $meet_all =  fetch_or_get($_POST['meet_all'], null);
    $process =  fetch_or_get($_POST['process'], '0');

    $info_data_fields =  array(
        'name'       => $name,
        'symbol'       => fetch_or_get($_POST['symbol'], null),
        'owner_id'       => $me["id"],
        'rewards'       => $rewards,
        'process'       => $process,
        'store_id'       => $limit_store_id,
        'begin'       => $limit_time_begin,
        'end'       => $limit_time_end,
        'qty'       => $qty,
        'pcodes'       => $limit_pcodes,
        'items'       => $items,
        // 'created_at'       => fetch_or_get($_POST['time'], null),
        'paid'       => $paid,
        'meet_1'       => $meet_1,
        'meet_all'       => $meet_all
    );

    foreach ($info_data_fields as $field_name => $field_val) {
        if ($field_name == 'name') {
            if (empty($field_val) || len_between($field_val, 3, 25) != true) {
                $data['err_code'] = "invalid_name";
                break;
            }
        }
        if ($field_name == 'rewards') {
            if ($field_val !== null && !is_numeric($field_val)) {
                $data['err_code'] = "invalid_rewards";
                break;
            }
        } else if ($field_name == 'symbol') {
            if (empty($field_val) || len_between($field_val, 3, 10) != true) {
                $data['err_code'] = "invalid_symbol";
                break;
            }
        } else if ($field_name == 'begin') {
            if (empty($field_val) || !strtotime($field_val)) {
                $data['err_code'] = "invalid_begin";
                break;
            }
        } else if ($field_name == 'end') {
            if (!empty($field_val) && !strtotime($field_val)) {
                $data['err_code'] = "invalid_end";
                break;
            }
        } else if ($field_name == 'qty') {
            if ($field_val !== null && !is_numeric($field_val)) {
                $data['err_code'] = "invalid_qty";
                break;
            }
        } else if ($field_name == 'items') {
            if ($field_val !== null && !is_numeric($field_val)) {
                $data['err_code'] = "invalid_items";
                break;
            }
        } else if ($field_name == 'paid') {
            if ($field_val !== null && !is_numeric($field_val)) {
                $data['err_code'] = "invalid_paid";
                break;
            }
        } else if ($field_name == 'meet_1') {
            if ($field_val !== null && !is_numeric($field_val)) {
                $data['err_code'] = "invalid_meet_1";
                break;
            }
        } else if ($field_name == 'meet_all') {
            if ($field_val !== null && !is_numeric($field_val)) {
                $data['err_code'] = "invalid_meet_all";
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

        $point_id = cl_db_insert(T_POINTS_INFO, $info_data_fields);
        if ($process) {
            // check limit
            $array_get = [];

            if ($limit_store_id) {
                $array_get['store_id'] = $limit_store_id;
            }
            $array_get['created_at'] = $limit_time_end ? array($limit_time_begin,  $limit_time_end) : array($limit_time_begin);
            $get_pcodes = '';
            if ($limit_pcodes) {
                foreach ($limit_pcodes as $pcode => $value) {
                    if ($pcode == 0) {
                        $get_pcodes = $value;
                    } else {
                        $get_pcodes .= ' OR ' . $value;
                    }
                }
                $array_get['ccode'] = $get_pcodes;
            }

            $cal_col = array(
                'phone',
                'customer_id',
                'SUM(qty) AS total_qty',
                'SUM(weight) AS total_weight',
                'SUM(amount) AS total_amount',
                'COUNT(DISTINCT pcode) AS unique_pcode_count'
            );


            $transaction_data_list = cl_db_get_items(T_TRANSACTION, $array_get, null, $cal_col, 'phone,customer_id');
            if ($transaction_data_list) {
                foreach ($transaction_data_list as $key => $transaction) {
                    // Calculate points transferred or received
                    $calculate['contact'] = $transaction['phone'];
                    $calculate['customer_id'] = $transaction['customer_id'];
                    $quotient1 = $qty ? intdiv($transaction['total_qty'], $qty) : 0;
                    $remainder1 = $quotient1 ? $transaction['total_qty'] % $qty : 0;

                    $quotient2 = $paid ? intdiv($transaction['total_amount'], $paid) : 0;
                    $remainder2 = $quotient2 ? $transaction['total_amount'] % $paid : 0;

                    $quotient3 = $items ? intdiv($transaction['unique_pcode_count'], $items) : 0;
                    $remainder3 = $quotient2 ? $transaction['unique_pcode_count'] % $items : 0;
                    $quotients = array_filter([$quotient1, $quotient2, $quotient3], function ($quotient) {
                        return $quotient != 0;
                    });
                    $quotient4 = ($qty && $paid && $items && $quotients) ? min($quotients) : 0;
                    $amount_meet_1 = $meet_1 ? ($quotient1 + $quotient2 + $quotient3) * $meet_1 : 0;
                    $amount_meet_all = $meet_all ? $quotient4 * $meet_all : 0;
                    $send_amount = $amount_meet_1 + $amount_meet_all;
                    $calculate['note'] = array(
                        'quotient_qty' => $quotient1,
                        'remainder_qty' => $remainder1,
                        'quotient_amount' => $quotient2,
                        'remainder_amount' => $remainder2,
                        'quotient_pcode' => $quotient3,
                        'remainder_pcode' => $remainder3,
                        'quotient_min' => $quotient4,
                        'meet_1' => $meet_1,
                        'meet_all' => $meet_all,
                        'amount_meet_1' => $amount_meet_1,
                        'amount_meet_all' => $amount_meet_all,
                    );

                    if ($rewards) {
                        if (($rewards - $send_amount) < 0) {
                            $data['msg'] = '';
                            return;
                        }
                        $rewards -= $send_amount;
                    }
                    cl_db_insert(T_POINTS_DISTRI, array(
                        "point_id" => $point_id,
                        "user_id" => $transaction['customer_id'],
                        "rewards" => $send_amount,
                        "phone" => $transaction['phone'],
                        "data_json" => json_encode($calculate['note']),
                    ));
                    if ($send_amount) {
                        $trans_id = cl_strf("TID_%s", sha1(microtime()));


                        $db->insert(T_POINTS_HISTORY, array(
                            "point_id" => $point_id,
                            "user_id" => $me["id"],
                            "operation" => '1',
                            "rewards" => $send_amount,
                            "trans_id" => $trans_id,
                            "json_data" => json_encode(array(
                                'customer_id' => $transaction['customer_id'],
                                'amount_meet_1' => $amount_meet_1,
                                'amount_meet_all' => $amount_meet_all,
                            )),
                        ));

                        $db->insert(T_POINTS_HISTORY, array(
                            "point_id" => $point_id,
                            "user_id" => $transaction['customer_id'],
                            "operation" => '2',
                            "rewards" => $send_amount,
                            "trans_id" => $trans_id,
                            "json_data" => json_encode($calculate['note']),
                        ));

                        cl_notify_user(array(
                            'subject'  => 'gift_point_transfer',
                            'user_id'  => $transaction['customer_id'],
                            'entry_id' => $me["id"],
                            'json' => cl_minify_js(json(array(
                                "trans_amount" => cl_money($send_amount)
                            ), true))
                        ));
                    }
                }
            }
        }
        
        cl_db_insert(T_POINTS_DISTRI, array(
            "point_id" => $point_id,
            "user_id" => $me["id"],
            "rewards" => $rewards,
        ));

    }
} else if ($action == "save_point") {
    $data['err_code'] =  0;
    $data['status']   =  400;
    $store_id = fetch_or_get($_POST['store'], null);
    $id = fetch_or_get($_POST['store'], null);

    $info_data_fields = cl_db_get_item(T_POINTS_INFO, array(
        'store_id' => $id,
        'owner_id' => $me["id"]
    ));
    if (!$info_data_fields) {
        return;
    }
    $distri = cl_db_get_item(T_POINTS_DISTRI, array(
        'point_id' => $info_data_fields['id'],
        'user_id' => $me["id"]
    ));

    if (!$distri) {
        return;
    }
    $rewards = floatval(fetch_or_get($_POST['rewards'], null));
    $rewards_use = floatval($info_data_fields['rewards']) - floatval($distri['rewards']);
    if ($rewards < $rewards_use) {
        $rewards = $distri['rewards'];
        $distri['rewards'] = 0;
    } else {
        $distri['rewards'] = $rewards - $rewards_use;
    }

    $check_store_id = $store_id ? cl_check_store($me["id"], $store_id) : false;
    $paid = fetch_or_get($_POST['paid'], null) ? str_replace(',', '', $_POST['paid']) : 0;
    $name = fetch_or_get($_POST['name'], null);
    $qty = fetch_or_get($_POST['qty'], 0);
    $items = fetch_or_get($_POST['items'], 0);

    $limit_store_id =  $check_store_id ? $store_id  : null;
    $limit_time_begin = formattedDateTime($_POST['begin'], null);
    $limit_time_end = formattedDateTime($_POST['end'], null);
    $limit_pcodes =  fetch_or_get($_POST['pcode_array'], null);
    $meet_1 = fetch_or_get($_POST['meet_1'], null);
    $meet_all =  fetch_or_get($_POST['meet_all'], null);
    $process =  fetch_or_get($_POST['process'], '0');

    $info_data_fields =  array_replace($info_data_fields, array(
        'name'       => $name,
        'symbol'       => fetch_or_get($_POST['symbol'], null),
        'owner_id'       => $me["id"],
        'rewards'       => $rewards,
        'process'       => $process,
        'store_id'       => $limit_store_id,
        'begin'       => $limit_time_begin,
        'end'       => $limit_time_end,
        'qty'       => $qty,
        'pcodes'       => $limit_pcodes,
        'items'       => $items,
        'updated_at'       => date('Y-m-d H:i:s'),
        'paid'       => $paid,
        'meet_1'       => $meet_1,
        'meet_all'       => $meet_all
    ));

    // foreach ($transaction_data_fields as $field_name => $field_val) {
    //     if ($field_name == 'ccode') {
    //         if (empty($field_val)) {
    //             // $data['err_code'] = "invalid_ccode"; break;
    //         } else if (len_between($field_val, 10, 25) != true) {
    //             $data['err_code'] = "invalid_ccode";
    //             break;
    //         }
    //     } else if ($field_name == 'phone') {
    //         if (empty($field_val) || len_between($field_val, 3, 25) != true) {
    //             $data['err_code'] = "invalid_phone";
    //             break;
    //         }
    //     } else if ($field_name == 'phone') {
    //         if (empty($field_val) || len_between($field_val, 3, 25) != true) {
    //             $data['err_code'] = "invalid_phone";
    //             break;
    //         }
    //     } else if ($field_name == 'points' && $field_val != 0) {
    //         if (empty($field_val) || !is_numeric($field_val)) {
    //             $data['err_code'] = "invalid_points";
    //             break;
    //         }

    //         if ($field_val > $send_amount) {
    //             $data['err_code'] = "lack_points";
    //             break;
    //         }
    //     } else if ($field_name == 'amount') {


    //         if ((empty($field_val) && ($field_val != 0) || !is_numeric($field_val))) {
    //             $data['err_code'] = "invalid_amount";
    //             break;
    //         }
    //     }
    // }
    if (empty($data['err_code'])) {
        $data['status'] = 200;

        cl_db_update(T_POINTS_INFO, array(
            "id" => $info_data_fields['id']
        ), $info_data_fields);
        cl_db_update(T_POINTS_DISTRI, array(
            'point_id' => $id,
            'user_id' => $me["id"]
        ), $distri);

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
