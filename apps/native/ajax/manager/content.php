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
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
} else {
    if ($action == 'load_more') {
        $data['err_code'] = 0;
        $data['status']   = 400;
        $offset           = fetch_or_get($_GET['offset'], 0);
        $type             = fetch_or_get($_GET['type'], false);
        $html_arr         = array();
        require_once(cl_full_path("core/apps/manager/app_ctrl.php"));
        $html_arr[] = cl_template('manager/includes/list_item');
    } elseif ($action === 'claim') {
        $id = $_POST['id'];
        $store = $_POST['store'];
        $game = $_POST['game'];
        $prize = $_POST['prize'];
        $date = date('Y-m-d H:m:s');
        //handle store

        $update = cl_db_update(T_PRIZE, array(
            "id" => $prize,
        ), array('claimed_at' => $date));
        if ($update) {
            $data['status'] = 200;
            $data['date'] = date('Y-m-d', strtotime($date));
        } else {
            $data['status'] = 500;
        }
        return $data;
    } elseif ($action === 'ticket') {
        $id = $_POST['id'];
        $store = $_POST['store'];
        $game = $_POST['game'];
        $ticket = $_POST['ticket'];
        cl_db_delete_item(T_TICKET, array(
            'id' => $ticket,
        ));
    }
}
