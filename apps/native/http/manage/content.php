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
}
else {
	require_once(cl_full_path("core/apps/manage/app_ctrl.php"));
	$cl["page_tab"]   = fetch_or_get($_GET["page"], "notifs");
	$cl["page_title"] = cl_translate("Manage");
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "manage";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;
	$cl["transactions"]     = cl_get_transactions(array(
		"limit"       => 50
	));

	$cl["edit_details"] = ($cl["page_tab"] == 'edit_invoice') ? cl_get_transaction_details($_GET["id"]) : '';

	foreach ($cl["edit_details"] as $key => $value) {
		if (!$value) {
			$cl["edit_details"][$key] = '';
		}
	}
	$cl["total_transactions"] = cl_get_total_transactions($cl["page_tab"]);
	$cl["http_res"]     = cl_template("manage/content");
}