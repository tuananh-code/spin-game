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

function cl_get_notifications($args = array())
{
	global $db, $cl, $me;

	$args        = (is_array($args)) ? $args : array();
	$options     = array(
        "offset" => false,
        "limit"  => 10,
        "type"   => "notifs"
    );

    $args   = array_merge($options, $args);
    $offset = $args['offset'];
    $limit  = $args['limit'];
    $type   = $args['type'];
	$sql    = cl_sqltepmlate('apps/notifications/sql/fetch_notifications', array(
		't_notifs' => T_NOTIFS,
		't_blocks' => T_BLOCKS,
		't_users'  => T_USERS,
		'offset'   => $offset,
		'user_id'  => $me['id'],
		'type'     => $type,
		'limit'    => $limit
	));

	$notifs = $db->rawQuery($sql);
	$data   = array();
	$update = array();

	if (cl_queryset($notifs)) {
		foreach ($notifs as $row) {
			$row['url']      = cl_link($row['username']);
			$row['avatar']   = cl_get_media($row["avatar"]);
			$row['time']     = cl_time2str($row["time"]);
			$row['name']     = cl_rn_strip($row['name']);
			$row['name']     = stripslashes($row['name']);
			$row['user_url'] = cl_link($row['username']);
			$row['json'] = json($row['json']);
			if (in_array($row['subject'], array('reply', 'repost', 'like', 'mention'))) {
				$row['url']     = cl_link(cl_strf("thread/%d", $row['entry_id']));
				$row['post_id'] = $row['entry_id'];
			} else if ($row['subject'] == "ad_approval") {
				$row['url'] = cl_link(cl_strf("ads/%d", $row['entry_id']));
			}
			// add notify to user when shop add event
			else if (in_array($row['subject'], array('subscribe_accept', 'subscribe', 'wallet_local_receipt', 'subscribe_request', 'visit', 'event', 'buy', 'prize', 'ticket', 'self', 'self_prize', 'self_ticket'))) {
				$row['user_id'] = $row['entry_id'];
				if (in_array($row['subject'], array('event'))) {
					$row['game'] = get_store_name_game($row['game_id']);
				}
				if (in_array($row['subject'], array('buy'))) {
					$row['game'] = get_store_name_game($row['game_id']);
					$row['product'] = $row['attr'];
				}
				if (in_array($row['subject'], array('ticket'))) {
					$row['url'] = cl_link('spin_prize');
					$row['game'] = get_store_name_game($row['game_id']);
					$row['ticket'] = $row['attr'];
				}
				if (in_array($row['subject'], array('ticket_exceed'))) {
					$row['url'] = cl_link('spin_prize');
					$row['exceed'] = $row['attr'];
				}
				if (in_array($row['subject'], array('self'))) {
					$row['all_user'] = $row['user_id_notify'];
				}
				if (in_array($row['subject'], array('prize'))) {
					$row['game'] = get_game_name($row['game_id']);
					$row['store'] = get_store_name_game($row['game_id']);
					$row['prize'] = get_prize_name($row['prize_id']);
				}
				if (in_array($row['subject'], array('self_prize'))) {
					$row['game'] = get_game_name($row['game_id']);
					$row['store'] = get_store_name_game($row['game_id']);
					$row['prize'] = get_prize_name($row['prize_id']);
					$row['customer'] = get_user_by_id($row['attr']);
					$row['url'] = cl_link('manager');
				}
				if (in_array($row['subject'], array('self_ticket'))) {
					$attr = explode(',', $row['attr']);
					$row['game'] = get_game_name($row['game_id']);
					$row['store'] = get_store_name_game($row['game_id']);
					$row['customer'] = get_user_by_id($attr[0]);
					$row['ticket'] = $attr[1];
					$row['url'] = cl_link('manager');
				}
			}
			if ($row['status'] == '0') {
				$update[] = $row['id'];
			}

			$data[] = $row;
		}
		if (not_empty($update)) {
			$db = $db->where('id', $update, 'IN');
			$qr = $db->update(T_NOTIFS, array('status' => '1'));
		}
	}

	return $data;
}

function cl_get_total_notifications($type = false)
{
	global $db, $cl, $me;

	$sql_query     = cl_sqltepmlate('apps/notifications/sql/fetch_total', array(
		't_notifs' => T_NOTIFS,
		't_blocks' => T_BLOCKS,
		't_users'  => T_USERS,
		'user_id'  => $me['id'],
		'type'     => $type
	));

	$total  = 0;
	$notifs = $db->rawQueryOne($sql_query);

	if (cl_queryset($notifs) && not_empty($notifs["total"])) {
		$total = $notifs["total"];
	}

	return $total;
}
