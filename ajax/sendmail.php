<?php

require_once __DIR__.'/../../../../wp-load.php';
require_once './../autoload.php';

if( !wp_verify_nonce($_POST['csrf-token'], Utils::getNonceActionName()) || (!empty($_POST['email']) && !Utils::emailValid($_POST['email'])) ){
    SendMail::badRequest();
}

// защита от бота
if(!empty($_POST['agree'])){
    SendMail::goodRequest();
}

$email = get_option('common_site_settings')['notification_email'];

$fields = [
	['company', 'Компания'],
	['name', 'Имя'],
	['tel', 'Номер телефона'],
	['email', 'E-mail'],
	['package', 'Пакет'],
	['message', 'Сообщение'],
];

require_once 'amocrm-integration.php';

$sendMail = new SendMail($fields, $email);
$sendMail->serveRequest();








/*
$email = get_option('common_site_settings')['notification_email'];
$subject = 'Test subject';
$message = '<h1>Test message</h1>';

$headers = "From: The Sender Name <test@testmail.com>\r\n";
$headers .= "Reply-To: reply@testmail.com\r\n";
$headers .= "Content-type: text/html\r\n";


$success = mail($email, $subject, $message, $headers);
echo json_encode(['success' => $success]);
*/
