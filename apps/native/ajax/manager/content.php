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
        require_once(cl_full_path("core/apps/managed/app_ctrl.php"));
        $html_arr[] = cl_template('managed/includes/list_item');
    } elseif ($action === 'add_store') {
        require_once(cl_full_path("core/apps/managed/app_ctrl.php"));
        $store = $_POST['store'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $phone = $_POST['phone'];
        $mail = $_POST['mail'];
        $date = date('Y-m-d H:m:s');
        //handle store
        $data = add_store($me['id'], $store, $address, $city, $phone, $mail);
        if ($data['status'] == 500) {
            return $data;
        } else {
            $id = cl_db_insert(T_STORE, array(
                "user_id" => $me['id'],
                "shop_name" => $store,
                "address" => $address,
                "city" => $city,
                "phone" => $phone,
                "mail" => $mail,
                "created_at" => $date
            ));
            if ($id) {
                $data['status'] = 200;
                return $data;
            } else {
                $data['status'] = 400;
                return $data;
            }
        }
    }
}
