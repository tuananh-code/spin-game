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
if (empty($cl["is_logged"])) {
	if ($cl["config"]["guest_page_status"] == "on") {
		cl_redirect("guest");
	}
	else{
		cl_redirect("feed");
	}
} else if (empty($cl['is_admin'])) {
	require_once cl_full_path("apps/native/http/err404/content.php");
}
else {
	require_once(cl_full_path("core/apps/point_system/app_ctrl.php"));
	$cl["page_tab"]   = fetch_or_get($_GET["page"], "inv");
	$cl["page_title"] = cl_translate("My point system");
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "point_system";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;
	$cl["information"]     = cl_get_information(array(
		"limit"       => 50
	));
	$cl["symbol"]        = '';
	$cl["list_distris"] = ($cl["page_tab"] == 'edit') ? cl_get_distris(array('id' => $_GET["id"])) : '';
	if ($cl["list_distris"]) {
		foreach ($cl["list_distris"] as $key => $value) {
			if ($value === []) {
				$cl["list_distris"][$key] = [];
			} else if ($value === 0) {
				$cl["list_distris"][$key] = 0;
			} else if (!$value) {
				$cl["list_distris"][$key] = '';
			}
		}

		foreach ($cl["information"] as $item) {
			if ($item["id"] === $_GET["id"]) {
				$cl["symbol"] = $item["symbol"];
				break;
			}
		}
	}
	$cl["stores"] = cl_get_list_stores();
	$cl["total_information"] = cl_get_total_information($cl["page_tab"]);
	$cl["http_res"]     = cl_template("point_system/content");
}