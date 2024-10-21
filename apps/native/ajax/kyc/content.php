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
$date = date('Y-m-d H:i:s');
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
        require_once(cl_full_path("core/apps/store/app_ctrl.php"));
        $html_arr[] = cl_template('store/includes/list_item');
    } elseif ($action === 'save_kyc') {
        $name = $_POST['cname'];
        $birth = $_POST['birth'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $identity = $_FILES['identity'];
        $image = $_FILES['image'];
        $info = [
            'name' => $name,
            'birth' => $birth,
            'address' => $address,
            'phone' => $phone,
        ];
        if (cl_db_get_item(T_KYC, array('user_id' => $me['id']))) {
            $data['status'] = 500;
            return $data;
        }
        if (count($identity['name']) !== 2) {
            $data['status'] = 600;
            return $data;
        }
        $dir = 'upload/images/' . date('Y') . '/' . date('m') . '/'; // Ensure there's a trailing slash

        $img_dir = $dir . $image['name'];
        $size = $image['size'];
        $tmp = $image['tmp_name'];
        if ($size > 5000000) {
            $data['status'] = 800;
            return $data;
        }
        $all_dir = [];
        $all_tmp = [];
        $all_size = [];
        $all_dir[] = $img_dir; // Add the main image directory
        $all_tmp[] = $tmp; // Add the temporary name of the main image
        $all_size[] = $size; // Add the size of the main image
        for ($i = 0; $i < count($identity['name']); $i++) {
            $all_dir[] = $dir . $identity['name'][$i];
            $all_tmp[] = $identity['tmp_name'][$i];
            $all_size[] = $identity['size'][$i];
            if ($identity['size'][$i] > 5000000) {
                $data['status'] = 800;
                return $data;
            }
        }
        $kyc_info = [
            'info' => $info,
            'img' => $all_dir
        ];
        $json = json_encode($kyc_info, true);

        for ($s = 0; $s < count($all_dir); $s++) {
            if (!move_uploaded_file($all_tmp[$s], $all_dir[$s])) {
                $data['status'] = 400;
                return $data;
            }
        }
        cl_db_insert(T_KYC, array(
            'user_id' => $me['id'],
            'kyc' => $json,
        ));
        $data['status'] = 200;
        return $data;
    } elseif ($action === 'delete_kyc') {
        $id = $_POST['kyc'];
        $delete = cl_db_update(T_KYC, array("id" => $id), array('delete_at' => $date));
        if ($delete) {
            $data['status'] = 200;
        } else {
            $data['status'] = 500;
        }
        return $data;
    } elseif ($action === 'verified_kyc') {
        $user = $_POST['user'];
        $kyc = $_POST['kyc'];
        $update = cl_db_update(T_KYC, array("id" => $kyc), array('verified_at' => $date));
        if ($update) {
            cl_db_update(T_USERS, array("id" => $user), array("verified_kyc" => '1'));
            $data['status'] = 200;
            $data['time'] = $date;
        } else {
            $data['status'] = 500;
        }
        return $data;
    }
}
