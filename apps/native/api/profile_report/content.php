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

if (empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 401,
		'data'    => array(),
		'message' => 'Unauthorized Access'
	);
}
else {

    $report_reason    = fetch_or_get($_POST['reason'], false); 
    $user_id          = fetch_or_get($_POST['user_id'], false); 
    $comment          = fetch_or_get($_POST['comment'], false); 
    $user_data        = cl_raw_user_data($user_id);

    if (empty($user_data) || $user_id == $me['id']) {
        $data['code']    = 400;
        $data['message'] = "User id is missing or invalid";
        $data['data']    = array();
    }

    else if(in_array($report_reason, array_keys($cl['profile_report_types'])) != true) {
        $data['code']    = 400;
        $data['message'] = "Report reason id is missing or invalid";
        $data['data']    = array();
    }

    else {
        cl_db_delete_item(T_PROF_REPORTS, array(
            'user_id' => $me['id'],
            'profile_id' => $user_id
        ));

        cl_db_insert(T_PROF_REPORTS, array(
            'user_id' => $me['id'],
            'profile_id' => $user_id,
            'reason'  => $report_reason,
            'comment' => (empty($comment)) ? "" : cl_croptxt($comment, 2900),
            'seen'    => '0',
            'time'    => time()
        ));

        $data['code']    = 200;
        $data['message'] = "Report sent successfully";
        $data['data']    = array();
    }
}