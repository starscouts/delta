<?php

use PHPMailer\PHPMailer\PHPMailer;

function url_get_contents($url) {
    $ret = [];
    exec("curl \"" . str_replace('"', '\\"', $url) . "\"", $ret);
    return $ret[0];
}

require $_SERVER['DOCUMENT_ROOT'] . '/includes/PHPMailer/src/Exception.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/PHPMailer/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/PHPMailer/src/SMTP.php';

$emailConfig = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email.json"), true);

function sendCode($email, $code) {
    global $emailConfig;

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'in-v3.mailjet.com';
    $mail->SMTPAuth = true;
    $mail->Username = $emailConfig["username"];
    $mail->Password = $emailConfig["password"];
    $mail->SMTPSecure = "none";
    $mail->Port = 587;

    $mail->setFrom('delta@equestria.dev', 'Delta');
    $mail->addAddress($email);
    $mail->addReplyTo(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email")), 'Equestria.dev');

    $mail->isHTML();
    $mail->Subject = l("lang_email_code_title");

    $body = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email.html");
    $body = str_replace("%3", date('Y'), $body);
    $body = str_replace("%2", l("lang_email_reasons_login"), $body);
    $body = str_replace("%1", "<p>" . l("lang_email_code_message_0") . "</p><p>" . l("lang_email_code_message_1") . "</p><p>" . $code . "</p><p><b>" . l("lang_email_code_message_2") . "</b></p><p>" . l("lang_email_code_message_3") . "</p>", $body);

    $mail->CharSet = 'UTF-8';
    $mail->Body = $body;
    $mail->AltBody = strip_tags($body);

    $mail->send();
}

function sendRegistration($email, $name, $id) {
    global $emailConfig;

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'in-v3.mailjet.com';
    $mail->SMTPAuth = true;
    $mail->Username = $emailConfig["username"];
    $mail->Password = $emailConfig["password"];
    $mail->SMTPSecure = "none";
    $mail->Port = 587;

    $mail->setFrom('delta@equestria.dev', 'Delta');
    $mail->addAddress($email);
    $mail->addReplyTo(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email")), 'Equestria.dev');

    $mail->isHTML();
    $mail->Subject = str_replace("%1", $id, l("lang_register_email_title"));
    $mail->addEmbeddedImage($_SERVER['DOCUMENT_ROOT'] . "/logo.png", "logo", "logo.png");

    $body = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email.html");
    $body = str_replace("%3", date('Y'), $body);
    $body = str_replace("%2", l("lang_register_email_reason"), $body);
    $body = str_replace("%1", "<p>" . str_replace("%1", strip_tags($name), l("lang_register_email_content_0")) . "</p><p>" . l("lang_register_email_content_1") . "</p><p>" . str_replace("%1", strip_tags($id), l("lang_register_email_content_2")) . "</p><p>" . l("lang_register_email_content_3") . "</p>", $body);

    $mail->CharSet = 'UTF-8';
    $mail->Body = $body;
    $mail->AltBody = strip_tags($body);

    $mail->send();
}

function sendRegistrationApproval($email, $name, $id) {
    global $emailConfig;

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'in-v3.mailjet.com';
    $mail->SMTPAuth = true;
    $mail->Username = $emailConfig["username"];
    $mail->Password = $emailConfig["password"];
    $mail->SMTPSecure = "none";
    $mail->Port = 587;

    $mail->setFrom('delta@equestria.dev', 'Delta');
    $mail->addAddress($email);
    $mail->addReplyTo(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email")), 'Equestria.dev');

    $mail->isHTML();
    $mail->Subject = str_replace("%1", $id, l("lang_register_approved_title"));
    $mail->addEmbeddedImage($_SERVER['DOCUMENT_ROOT'] . "/logo.png", "logo", "logo.png");

    $body = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email.html");
    $body = str_replace("%3", date('Y'), $body);
    $body = str_replace("%2", l("lang_register_email_reason"), $body);
    $body = str_replace("%1", "<p>" . str_replace("%1", strip_tags($name), l("lang_register_approved_content_0")) . "</p><p>" . l("lang_register_approved_content_1") . "</p><p>" . l("lang_register_approved_content_2") . "</p><p>" . l("lang_register_approved_content_3") . "</p>", $body);

    $mail->CharSet = 'UTF-8';
    $mail->Body = $body;
    $mail->AltBody = strip_tags($body);

    $mail->send();
}

function sendRegistrationRejection($email, $name, $id, $reason) {
    global $emailConfig;

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'in-v3.mailjet.com';
    $mail->SMTPAuth = true;
    $mail->Username = $emailConfig["username"];
    $mail->Password = $emailConfig["password"];
    $mail->SMTPSecure = "none";
    $mail->Port = 587;

    $mail->setFrom('delta@equestria.dev', 'Delta');
    $mail->addAddress($email);
    $mail->addReplyTo(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email")), 'Equestria.dev');

    $mail->isHTML();
    $mail->Subject = str_replace("%1", $id, l("lang_register_rejected_title"));
    $mail->addEmbeddedImage($_SERVER['DOCUMENT_ROOT'] . "/logo.png", "logo", "logo.png");

    $body = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email.html");
    $body = str_replace("%3", date('Y'), $body);
    $body = str_replace("%2", l("lang_register_email_reason"), $body);
    $body = str_replace("%1", "<p>" . str_replace("%1", strip_tags($name), l("lang_register_rejected_content_0")) . "</p><p>" . str_replace("%1", (isset($reason) && trim($reason) !== "" ? l("lang_register_rejected_content_5") . "<blockquote>" . strip_tags($reason) . "</blockquote>" : l("lang_register_rejected_content_4")), l("lang_register_rejected_content_1")) . "</p><p>" . l("lang_register_rejected_content_2") . "</p><p>" . l("lang_register_rejected_content_3") . "</p>", $body);

    $mail->CharSet = 'UTF-8';
    $mail->Body = $body;
    $mail->AltBody = strip_tags($body);

    $mail->send();
}

function sendAlerts($email, $alerts) {
    global $emailConfig;

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'in-v3.mailjet.com';
    $mail->SMTPAuth = true;
    $mail->Username = $emailConfig["username"];
    $mail->Password = $emailConfig["password"];
    $mail->SMTPSecure = "none";
    $mail->Port = 587;

    $mail->setFrom('delta@equestria.dev', 'Delta');
    $mail->addAddress($email);
    $mail->addReplyTo(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email")), 'Equestria.dev');

    $mail->isHTML();

    if (count($alerts) > 1) {
        $mail->Subject = str_replace("%1", count($alerts), l("lang_email_alerts_title_0"));
    } else {
        $mail->Subject = l("lang_email_alerts_title_1");
    }
    $mail->addEmbeddedImage($_SERVER['DOCUMENT_ROOT'] . "/logo.png", "logo", "logo.png");

    $body = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email.html");
    $body = str_replace("%3", date('Y'), $body);
    $body = str_replace("%2", l("lang_email_reasons_alert"), $body);
    $text = "<p>" . l("lang_email_alerts_message_0") . "</p><p>" . l("lang_email_alerts_message_1") . "</p>";

    foreach ($alerts as $alert) {
        $text .= "<p><b>" . $alert["title"] . "</b><br>" . $alert["message"] . "</p>";
    }

    $text .= "<p>" . l("lang_email_alerts_message_2") . "</p>";
    $body = str_replace("%1", $text, $body);

    $mail->CharSet = 'UTF-8';
    $mail->Body = $body;
    $mail->AltBody = strip_tags($body);

    $mail->send();
}

function sendLogin($email) {
    global $emailConfig;

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'in-v3.mailjet.com';
    $mail->SMTPAuth = true;
    $mail->Username = $emailConfig["username"];
    $mail->Password = $emailConfig["password"];
    $mail->SMTPSecure = "none";
    $mail->Port = 587;

    $mail->setFrom('delta@equestria.dev', 'Delta');
    $mail->addAddress($email);
    $mail->addReplyTo(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email")), 'Equestria.dev');

    $mail->isHTML();
    $mail->Subject = l("lang_email_login_title");
    $mail->addEmbeddedImage($_SERVER['DOCUMENT_ROOT'] . "/logo.png", "logo", "logo.png");

    $location = json_decode(url_get_contents("https://api.iplocation.net/?ip=" . $_SERVER['HTTP_X_FORWARDED_FOR']), true);

    $body = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email.html");
    $body = str_replace("%3", date('Y'), $body);
    $body = str_replace("%2", l("lang_email_reasons_login2"), $body);
    $body = str_replace("%1", "<p>" . l("lang_email_login_message_0") . "</p><p>" . l("lang_email_login_message_1") . "</p><p>" . $location["country_name"] . " (" . $_SERVER['HTTP_X_FORWARDED_FOR'] . ", " . $location["isp"] . ")<br>" . $_SERVER["HTTP_USER_AGENT"] . "</p><p>" . l("lang_email_login_message_2") . "</p><p>" . l("lang_email_login_message_3") . "</p>", $body);

    $mail->CharSet = 'UTF-8';
    $mail->Body = $body;
    $mail->AltBody = strip_tags($body);

    $mail->send();
}
