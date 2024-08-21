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

function cl_get_transactions($args = array()) {
	global $db, $cl, $me;

	$args        = (is_array($args)) ? $args : array();
	$options     = array(
        "offset" => false,
        "limit"  => 10
    );

    $args   = array_merge($options, $args);
    $limit  = $args['limit'];
	$sql    = cl_sqltepmlate('apps/manage/sql/fetch_transactions', array(
		't_transaction' => T_TRANSACTION,
		'user_id'  => $me['id'],
		'limit'  => $limit
	));
	$transactions = $db->rawQuery($sql);
	$data   = array();
	$update = array();


	if (cl_queryset($transactions)) {
		foreach ($transactions as $row) {
			// $row['url']      = cl_link($row['username']);
			// $row['avatar']   = cl_get_media($row["avatar"]);
			// $row['time']     = cl_time2str($row["time"]);
			// $row['name']     = cl_rn_strip($row['name']);
            // $row['name']     = stripslashes($row['name']);
            // $row['user_url'] = cl_link($row['username']);
            // $row['json'] = json($row['json']);

			// if (in_array($row['subject'], array('reply', 'repost', 'like', 'mention'))) {
			// 	$row['url']     = cl_link(cl_strf("thread/%d", $row['entry_id']));
			// 	$row['post_id'] = $row['entry_id'];
			// }

			// else if ($row['subject'] == "ad_approval") {
			// 	$row['url'] = cl_link(cl_strf("ads/%d", $row['entry_id']));
			// }

			// else if (in_array($row['subject'], array('subscribe_accept', 'subscribe', 'wallet_local_receipt', 'subscribe_request', 'visit'))) {
			// 	$row['user_id'] = $row['entry_id'];
			// }

			// if ($row['status'] == '0') {
			// 	$update[] = $row['id'];
			// }

			$data[] = $row;
		}

		// if (not_empty($update)) {
		// 	$db = $db->where('id', $update, 'IN');
		// 	$qr = $db->update(T_NOTIFS, array('status' => '1'));
		// }
	}

	return $data;
}

function cl_get_total_transactions($type = false) {
	global $db, $cl, $me;

	$sql_query     = cl_sqltepmlate('apps/manage/sql/fetch_total', array(
		't_transactions' => T_TRANSACTION,
		'user_id'  => $me['id'],
	));

	$total  = 0;
	$transaction = $db->rawQueryOne($sql_query);

	if (cl_queryset($transaction) && not_empty($transaction["total"])) {
		$total = $transaction["total"];
	}

	return $total;
}
