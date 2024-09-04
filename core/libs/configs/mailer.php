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

require_once(cl_full_path('core/libs/PHPMailer/PHPMailerAutoload.php'));
function mailbox($mail_reply, $send_reply = true, $store, $event,)
{
    global $me;
    $mail = new PHPMailer(true);
    $mailbox = $me['email'];
    $mail_pass = 'qdshksprkgcshynz';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $mailbox;
    $mail->Password = $mail_pass;
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    // $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;

    $mail->setFrom($mailbox, 'Mycheery.com ');
    $mail->addAddress($mail_reply);
    $mail->Subject = 'Mail Notify: We create new event with store: ' . $store;
    // Set the HTML body
    $mail->isHTML(true);
    // $mail->Body = $send_reply
    //     ? '<center><h2 style="margin:.5em;">User domain: ' . '</h2><h2 style="margin:.5em;">Host require change to: ' . $website . '</h2><h2 style="margin:.5em;">User mail: ' . $mail_sub . '</h2></center>'
    //     : include 'mail_format.php';
    // if ($send_reply) {
    //     $body = '<center><h2 style="margin:.5em;">User domain: ' . '</h2><h2 style="margin:.5em;">Host require change to: ' . $website . '</h2><h2 style="margin:.5em;">User mail: ' . $mail_sub . '</h2></center>';
    // } else {
    //     ob_start();
    //     include 'mail_format.php';
    //     $body = ob_get_clean();
    // }
    $body = 'We create new event with store: ' . $store . ' Check now';
    $mail->Body = $body;
    // Optional: Set reply-to address
    // Send the email
    if ($mail->send()) {
        // wp_send_json_success('success');
        $data = true;
    } else {
        // wp_send_json_error('Failed to send email. Error: ' . $mail->ErrorInfo);
        $data = false;
    }
    return $data;
}
